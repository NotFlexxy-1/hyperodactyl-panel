<?php

namespace Hyperodactyl\Transformers\Api\Client;

use Hyperodactyl\Models\LxcContainer;

class LxcContainerTransformer extends BaseClientTransformer
{
    public function getResourceName(): string
    {
        return 'lxc_container';
    }

    public function transform(LxcContainer $container): array
    {
        return [
            'allocations' => $container->allocations->map(fn ($a) => [
                'id' => $a->id,
                'protocol' => $a->protocol,
                'host_port' => $a->host_port,
                'container_port' => $a->container_port,
            ])->all(),
            'uuid' => $container->uuid,
            'identifier' => $container->uuid_short,
            'name' => $container->name,
            'description' => $container->description,
            'status' => $container->status,
            'image' => $container->image,
            'limits' => [
                'memory' => $container->memory,
                'swap' => $container->swap,
                'disk' => $container->disk,
                'cpu' => $container->cpu_limit,
                'io' => $container->io_weight,
            ],
            'ip_address' => $container->ip_address,
            'node' => [
                'uuid' => $container->node->uuid,
                'name' => $container->node->name,
                'driver' => $container->node->driver,
            ],
            'installed_at' => $container->installed_at?->toIso8601String(),
            'created_at' => $container->created_at?->toIso8601String(),
        ];
    }
}
