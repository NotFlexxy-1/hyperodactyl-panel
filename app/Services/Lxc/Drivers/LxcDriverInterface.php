<?php

namespace Hyperodactyl\Services\Lxc\Drivers;

use Hyperodactyl\Models\LxcNode;
use Hyperodactyl\Models\LxcContainer;
use Hyperodactyl\Exceptions\Lxc\LxcApiException;

/**
 * Contract implemented by every real remote-infrastructure driver (LXD, Proxmox, ...).
 * Every method must perform a genuine HTTP call against the node and either
 * return real data or throw LxcApiException — no method may fabricate a result.
 */
interface LxcDriverInterface
{
    public function setNode(LxcNode $node): self;

    /**
     * Create the container/instance on the remote node and wait for the creation
     * operation to complete.
     *
     * @throws LxcApiException
     */
    public function create(LxcContainer $container): void;

    /**
     * @throws LxcApiException
     */
    public function delete(LxcContainer $container): void;

    /**
     * @throws LxcApiException
     */
    public function start(LxcContainer $container): void;

    /**
     * @throws LxcApiException
     */
    public function stop(LxcContainer $container): void;

    /**
     * @throws LxcApiException
     */
    public function restart(LxcContainer $container): void;

    /**
     * @throws LxcApiException
     */
    public function freeze(LxcContainer $container): void;

    /**
     * Return the real, current power state as reported by the remote API.
     *
     * @throws LxcApiException
     */
    public function state(LxcContainer $container): string;

    /**
     * Return real, live resource utilization (cpu/memory/disk/network) as reported
     * by the remote API.
     *
     * @throws LxcApiException
     */
    public function resources(LxcContainer $container): array;

    /**
     * Push a new set of resource limits to the remote node.
     *
     * @throws LxcApiException
     */
    public function updateLimits(LxcContainer $container): void;

    /**
     * Return a real, freshly issued console/exec URL (websocket) for the container.
     *
     * @throws LxcApiException
     */
    public function consoleUrl(LxcContainer $container): array;

    /**
     * @throws LxcApiException
     */
    public function listImages(): array;

    /**
     * @throws LxcApiException
     */
    public function createSnapshot(LxcContainer $container, string $name): array;

    /**
     * @throws LxcApiException
     */
    public function listSnapshots(LxcContainer $container): array;

    /**
     * @throws LxcApiException
     */
    public function restoreSnapshot(LxcContainer $container, string $name): void;

    /**
     * Return real, current node-level resource usage as reported by the remote API.
     *
     * @throws LxcApiException
     */
    public function nodeUsage(): array;
}
