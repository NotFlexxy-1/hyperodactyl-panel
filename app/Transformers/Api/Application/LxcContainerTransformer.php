<?php

namespace Hyperodactyl\Transformers\Api\Application;

use Hyperodactyl\Models\LxcContainer;

class LxcContainerTransformer extends BaseTransformer
{
    public function getResourceName(): string
    {
        return 'lxc_container';
    }

    public function transform(LxcContainer $container): array
    {
        return [
            'id' => $container->id,
            'uuid' => $container->uuid,
            'uuid_short' => $container->uuid_short,
            'name' => $container->name,
            'description' => $container->description,
            'owner_id' => $container->owner_id,
            'lxc_node_id' => $container->lxc_node_id,
            'image' => $container->image,
            'status' => $container->status,
            'memory' => $container->memory,
            'swap' => $container->swap,
            'disk' => $container->disk,
            'cpu_limit' => $container->cpu_limit,
            'cpu_pinning' => $container->cpu_pinning,
            'io_weight' => $container->io_weight,
            'ip_address' => $container->ip_address,
            'mac' => $container->mac,
            'installed_at' => $this->formatTimestamp($container->installed_at ?? $container->created_at),
            'created_at' => $this->formatTimestamp($container->created_at),
            'updated_at' => $this->formatTimestamp($container->updated_at),
        ];
    }
}
