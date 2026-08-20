<?php

namespace Hyperodactyl\Transformers\Api\Application;

use Hyperodactyl\Models\LxcNode;

class LxcNodeTransformer extends BaseTransformer
{
    public function getResourceName(): string
    {
        return 'lxc_node';
    }

    public function transform(LxcNode $node): array
    {
        return [
            'id' => $node->id,
            'uuid' => $node->uuid,
            'name' => $node->name,
            'description' => $node->description,
            'fqdn' => $node->fqdn,
            'scheme' => $node->scheme,
            'port' => $node->port,
            'driver' => $node->driver,
            'tls_verify' => $node->tls_verify,
            'proxmox_node' => $node->proxmox_node,
            'storage_pool' => $node->storage_pool,
            'network_bridge' => $node->network_bridge,
            'maintenance_mode' => $node->maintenance_mode,
            'memory' => $node->memory,
            'memory_overallocate' => $node->memory_overallocate,
            'disk' => $node->disk,
            'disk_overallocate' => $node->disk_overallocate,
            'cpu' => $node->cpu,
            'cpu_overallocate' => $node->cpu_overallocate,
            'containers_count' => $node->containers()->count(),
            'created_at' => $this->formatTimestamp($node->created_at),
            'updated_at' => $this->formatTimestamp($node->updated_at),
        ];
    }
}
