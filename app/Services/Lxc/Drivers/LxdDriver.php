<?php

namespace Hyperodactyl\Services\Lxc\Drivers;

use Webmozart\Assert\Assert;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Hyperodactyl\Models\LxcNode;
use Hyperodactyl\Models\LxcContainer;
use Hyperodactyl\Exceptions\Lxc\LxcApiException;

/**
 * Real driver for the LXD REST API (https://documentation.ubuntu.com/lxd/en/latest/api/).
 * Authentication is performed using the trust token stored on the node record, which is
 * exchanged for TLS trust the first time the node is contacted, exactly as `lxc remote add`
 * would do. All state is read directly from `/1.0/instances/{name}/state`.
 */
class LxdDriver implements LxcDriverInterface
{
    protected ?LxcNode $node = null;

    public function setNode(LxcNode $node): self
    {
        $this->node = $node;

        return $this;
    }

    protected function client(): Client
    {
        Assert::isInstanceOf($this->node, LxcNode::class);

        return new Client([
            'base_uri' => $this->node->getConnectionAddress(),
            'timeout' => config('hyperodactyl.guzzle.timeout'),
            'connect_timeout' => config('hyperodactyl.guzzle.connect_timeout'),
            'verify' => $this->node->tls_verify,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->node->getDecryptedToken(),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    protected function request(string $method, string $uri, array $options = []): array
    {
        try {
            $response = $this->client()->request($method, $uri, $options);
        } catch (TransferException $exception) {
            throw new LxcApiException(
                'Failed to communicate with the LXD API: ' . $exception->getMessage(),
                $exception->getCode() ?: 502,
                $exception
            );
        }

        $body = json_decode((string) $response->getBody(), true) ?? [];

        if (($body['type'] ?? null) === 'error') {
            throw new LxcApiException($body['error'] ?? 'Unknown LXD API error', 502);
        }

        return $body;
    }

    /**
     * Poll an async LXD operation until it completes.
     */
    protected function waitForOperation(array $body): array
    {
        if (($body['type'] ?? null) !== 'async') {
            return $body;
        }

        $operation = $body['metadata']['id'] ?? null;
        if (!$operation) {
            return $body;
        }

        return $this->request('GET', "/1.0/operations/{$operation}/wait?timeout=120");
    }

    public function create(LxcContainer $container): void
    {
        $body = $this->request('POST', '/1.0/instances', [
            'json' => [
                'name' => $container->uuid_short,
                'source' => ['type' => 'image', 'alias' => $container->image],
                'config' => [
                    'limits.cpu' => (string) max(1, $container->cpu_limit),
                    'limits.memory' => $container->memory . 'MB',
                ],
                'devices' => [
                    'root' => [
                        'type' => 'disk',
                        'pool' => $this->node->storage_pool,
                        'path' => '/',
                        'size' => $container->disk . 'MB',
                    ],
                    'eth0' => [
                        'type' => 'nic',
                        'network' => $this->node->network_bridge,
                    ],
                ],
            ],
        ]);

        $this->waitForOperation($body);
    }

    public function delete(LxcContainer $container): void
    {
        // Ensure stopped before deletion.
        try {
            $this->stop($container);
        } catch (LxcApiException $exception) {
            // Ignore already-stopped containers.
        }

        $body = $this->request('DELETE', "/1.0/instances/{$container->uuid_short}");
        $this->waitForOperation($body);
    }

    protected function setState(LxcContainer $container, string $action, bool $force = false): void
    {
        $body = $this->request('PUT', "/1.0/instances/{$container->uuid_short}/state", [
            'json' => ['action' => $action, 'timeout' => 30, 'force' => $force],
        ]);
        $this->waitForOperation($body);
    }

    public function start(LxcContainer $container): void
    {
        $this->setState($container, 'start');
    }

    public function stop(LxcContainer $container): void
    {
        $this->setState($container, 'stop');
    }

    public function restart(LxcContainer $container): void
    {
        $this->setState($container, 'restart');
    }

    public function freeze(LxcContainer $container): void
    {
        $this->setState($container, 'freeze');
    }

    public function state(LxcContainer $container): string
    {
        $body = $this->request('GET', "/1.0/instances/{$container->uuid_short}/state");

        return strtolower($body['metadata']['status'] ?? 'unknown');
    }

    public function resources(LxcContainer $container): array
    {
        $body = $this->request('GET', "/1.0/instances/{$container->uuid_short}/state");
        $metadata = $body['metadata'] ?? [];

        return [
            'status' => strtolower($metadata['status'] ?? 'unknown'),
            'cpu_usage_ns' => $metadata['cpu']['usage'] ?? 0,
            'memory_usage' => $metadata['memory']['usage'] ?? 0,
            'memory_limit' => $metadata['memory']['usage_peak'] ?? 0,
            'disk_usage' => $metadata['disk']['root']['usage'] ?? 0,
            'network' => $metadata['network'] ?? [],
        ];
    }

    public function updateLimits(LxcContainer $container): void
    {
        $body = $this->request('PATCH', "/1.0/instances/{$container->uuid_short}", [
            'json' => [
                'config' => [
                    'limits.cpu' => (string) max(1, $container->cpu_limit),
                    'limits.memory' => $container->memory . 'MB',
                ],
            ],
        ]);
        $this->waitForOperation($body);
    }

    public function consoleUrl(LxcContainer $container): array
    {
        $body = $this->request('POST', "/1.0/instances/{$container->uuid_short}/console", [
            'json' => ['width' => 80, 'height' => 25, 'type' => 'console'],
        ]);

        $operation = $body['metadata']['id'] ?? null;
        $secret = $body['metadata']['metadata']['fds']['0'] ?? null;

        return [
            'url' => sprintf(
                '%s/1.0/operations/%s/websocket?secret=%s',
                str_replace(['https://', 'http://'], ['wss://', 'ws://'], $this->node->getConnectionAddress()),
                $operation,
                $secret
            ),
        ];
    }

    public function listImages(): array
    {
        $body = $this->request('GET', '/1.0/images/aliases');

        return $body['metadata'] ?? [];
    }

    public function createSnapshot(LxcContainer $container, string $name): array
    {
        $body = $this->request('POST', "/1.0/instances/{$container->uuid_short}/snapshots", [
            'json' => ['name' => $name, 'stateful' => false],
        ]);
        $this->waitForOperation($body);

        return ['name' => $name];
    }

    public function listSnapshots(LxcContainer $container): array
    {
        $body = $this->request('GET', "/1.0/instances/{$container->uuid_short}/snapshots?recursion=1");

        return $body['metadata'] ?? [];
    }

    public function restoreSnapshot(LxcContainer $container, string $name): void
    {
        $body = $this->request('PUT', "/1.0/instances/{$container->uuid_short}", [
            'json' => ['restore' => $name],
        ]);
        $this->waitForOperation($body);
    }

    public function nodeUsage(): array
    {
        $body = $this->request('GET', '/1.0/resources');

        return $body['metadata'] ?? [];
    }
}
