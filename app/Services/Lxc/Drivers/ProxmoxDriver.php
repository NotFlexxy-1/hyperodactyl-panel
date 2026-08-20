<?php

namespace Hyperodactyl\Services\Lxc\Drivers;

use Webmozart\Assert\Assert;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Hyperodactyl\Models\LxcNode;
use Hyperodactyl\Models\LxcContainer;
use Hyperodactyl\Exceptions\Lxc\LxcApiException;

/**
 * Real driver for the Proxmox VE REST API (https://pve.proxmox.com/pve-docs/api-viewer/).
 * Authenticates using an API token (`PVEAPIToken=user@realm!tokenid=secret`) stored on the
 * node record. Long running actions return a UPID which is polled via the tasks endpoint.
 */
class ProxmoxDriver implements LxcDriverInterface
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
                'Authorization' => 'PVEAPIToken=' . $this->node->getDecryptedToken() . '=' . $this->node->getDecryptedSecret(),
                'Accept' => 'application/json',
            ],
        ]);
    }

    protected function request(string $method, string $uri, array $options = []): array
    {
        try {
            $response = $this->client()->request($method, $uri, $options);
        } catch (TransferException $exception) {
            throw new LxcApiException(
                'Failed to communicate with the Proxmox API: ' . $exception->getMessage(),
                $exception->getCode() ?: 502,
                $exception
            );
        }

        $decoded = json_decode((string) $response->getBody(), true);
        if (!is_array($decoded)) {
            throw new LxcApiException('Received a malformed response from the Proxmox API.', 502);
        }

        return $decoded;
    }

    protected function base(): string
    {
        Assert::notEmpty($this->node->proxmox_node, 'A Proxmox node must be configured on this LxcNode.');

        return "/api2/json/nodes/{$this->node->proxmox_node}/lxc";
    }

    protected function vmid(LxcContainer $container): int
    {
        // Deterministically derive a numeric VMID from the container id; Proxmox requires ints.
        return 100000 + $container->id;
    }

    protected function waitForTask(string $upid): void
    {
        $deadline = now()->addSeconds(120);
        do {
            $body = $this->request('GET', "/api2/json/nodes/{$this->node->proxmox_node}/tasks/{$upid}/status");
            $status = $body['data']['status'] ?? null;
            if ($status === 'stopped') {
                if (($body['data']['exitstatus'] ?? 'OK') !== 'OK') {
                    throw new LxcApiException('Proxmox task failed: ' . ($body['data']['exitstatus'] ?? 'unknown error'));
                }

                return;
            }
            usleep(500000);
        } while (now()->lt($deadline));

        throw new LxcApiException('Timed out waiting for Proxmox task to complete.');
    }

    public function create(LxcContainer $container): void
    {
        $body = $this->request('POST', $this->base(), [
            'form_params' => [
                'vmid' => $this->vmid($container),
                'hostname' => $container->uuid_short,
                'ostemplate' => $container->image,
                'memory' => $container->memory,
                'swap' => $container->swap,
                'cores' => max(1, $container->cpu_limit),
                'rootfs' => "{$this->node->storage_pool}:{$container->disk}",
                'net0' => "name=eth0,bridge={$this->node->network_bridge},ip=dhcp",
            ],
        ]);

        $this->waitForTask($body['data']);
    }

    public function delete(LxcContainer $container): void
    {
        try {
            $this->stop($container);
        } catch (LxcApiException $exception) {
            // Already stopped.
        }

        $body = $this->request('DELETE', $this->base() . '/' . $this->vmid($container));
        $this->waitForTask($body['data']);
    }

    public function start(LxcContainer $container): void
    {
        $body = $this->request('POST', $this->base() . '/' . $this->vmid($container) . '/status/start');
        $this->waitForTask($body['data']);
    }

    public function stop(LxcContainer $container): void
    {
        $body = $this->request('POST', $this->base() . '/' . $this->vmid($container) . '/status/shutdown');
        $this->waitForTask($body['data']);
    }

    public function restart(LxcContainer $container): void
    {
        $this->stop($container);
        $this->start($container);
    }

    public function freeze(LxcContainer $container): void
    {
        $body = $this->request('POST', $this->base() . '/' . $this->vmid($container) . '/status/suspend');
        $this->waitForTask($body['data']);
    }

    public function state(LxcContainer $container): string
    {
        $body = $this->request('GET', $this->base() . '/' . $this->vmid($container) . '/status/current');

        return strtolower($body['data']['status'] ?? 'unknown');
    }

    public function resources(LxcContainer $container): array
    {
        $body = $this->request('GET', $this->base() . '/' . $this->vmid($container) . '/status/current');
        $data = $body['data'] ?? [];

        return [
            'status' => strtolower($data['status'] ?? 'unknown'),
            'cpu_usage_ns' => $data['cpu'] ?? 0,
            'memory_usage' => $data['mem'] ?? 0,
            'memory_limit' => $data['maxmem'] ?? 0,
            'disk_usage' => $data['disk'] ?? 0,
            'network' => ['in' => $data['netin'] ?? 0, 'out' => $data['netout'] ?? 0],
        ];
    }

    public function updateLimits(LxcContainer $container): void
    {
        $this->request('PUT', $this->base() . '/' . $this->vmid($container) . '/config', [
            'form_params' => [
                'memory' => $container->memory,
                'swap' => $container->swap,
                'cores' => max(1, $container->cpu_limit),
            ],
        ]);
    }

    public function consoleUrl(LxcContainer $container): array
    {
        $body = $this->request('POST', $this->base() . '/' . $this->vmid($container) . '/termproxy');
        $data = $body['data'] ?? [];

        $host = parse_url($this->node->getConnectionAddress(), PHP_URL_HOST);

        return [
            'url' => sprintf(
                'wss://%s:%s/api2/json/nodes/%s/lxc/%s/vncwebsocket?port=%s&vncticket=%s',
                $host,
                $this->node->port,
                $this->node->proxmox_node,
                $this->vmid($container),
                $data['port'] ?? '',
                $data['ticket'] ?? ''
            ),
        ];
    }

    public function listImages(): array
    {
        $body = $this->request('GET', "/api2/json/nodes/{$this->node->proxmox_node}/storage/{$this->node->storage_pool}/content?content=vztmpl");

        return $body['data'] ?? [];
    }

    public function createSnapshot(LxcContainer $container, string $name): array
    {
        $body = $this->request('POST', $this->base() . '/' . $this->vmid($container) . '/snapshot', [
            'form_params' => ['snapname' => $name],
        ]);
        $this->waitForTask($body['data']);

        return ['name' => $name];
    }

    public function listSnapshots(LxcContainer $container): array
    {
        $body = $this->request('GET', $this->base() . '/' . $this->vmid($container) . '/snapshot');

        return $body['data'] ?? [];
    }

    public function restoreSnapshot(LxcContainer $container, string $name): void
    {
        $body = $this->request('POST', $this->base() . '/' . $this->vmid($container) . '/snapshot/' . $name . '/rollback');
        $this->waitForTask($body['data']);
    }

    public function nodeUsage(): array
    {
        $body = $this->request('GET', "/api2/json/nodes/{$this->node->proxmox_node}/status");

        return $body['data'] ?? [];
    }
}
