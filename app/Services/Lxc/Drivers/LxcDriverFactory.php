<?php

namespace Hyperodactyl\Services\Lxc\Drivers;

use Hyperodactyl\Models\LxcNode;
use Illuminate\Container\Container;
use InvalidArgumentException;

class LxcDriverFactory
{
    public function __construct(private Container $container)
    {
    }

    public function forNode(LxcNode $node): LxcDriverInterface
    {
        $driver = match ($node->driver) {
            LxcNode::DRIVER_LXD => LxdDriver::class,
            LxcNode::DRIVER_PROXMOX => ProxmoxDriver::class,
            default => throw new InvalidArgumentException("Unsupported LXC driver [{$node->driver}]."),
        };

        return $this->container->make($driver)->setNode($node);
    }
}
