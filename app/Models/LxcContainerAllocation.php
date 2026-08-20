<?php

namespace Hyperodactyl\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $lxc_container_id
 * @property string $protocol
 * @property int $host_port
 * @property int $container_port
 */
class LxcContainerAllocation extends Model
{
    protected $table = 'lxc_container_allocations';

    protected $casts = [
        'host_port' => 'int',
        'container_port' => 'int',
    ];

    public function container(): BelongsTo
    {
        return $this->belongsTo(LxcContainer::class, 'lxc_container_id');
    }
}
