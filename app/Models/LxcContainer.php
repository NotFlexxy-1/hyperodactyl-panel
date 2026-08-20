<?php

namespace Hyperodactyl\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $uuid
 * @property string $uuid_short
 * @property string $name
 * @property string|null $description
 * @property int $owner_id
 * @property int $lxc_node_id
 * @property string $image
 * @property string|null $status
 * @property int $memory
 * @property int $swap
 * @property int $disk
 * @property int $cpu_limit
 * @property string|null $cpu_pinning
 * @property int $io_weight
 * @property string|null $ip_address
 * @property string|null $mac
 * @property string|null $ssh_key
 * @property string|null $root_password
 * @property \Illuminate\Support\Carbon|null $installed_at
 * @property LxcNode $node
 * @property User $owner
 */
class LxcContainer extends Model
{
    public const STATUS_INSTALLING = 'installing';
    public const STATUS_INSTALL_FAILED = 'install_failed';
    public const STATUS_RUNNING = 'running';
    public const STATUS_STOPPED = 'stopped';
    public const STATUS_SUSPENDED = 'suspended';

    protected $table = 'lxc_containers';

    protected $casts = [
        'memory' => 'int',
        'swap' => 'int',
        'disk' => 'int',
        'cpu_limit' => 'int',
        'io_weight' => 'int',
        'installed_at' => 'datetime',
    ];

    protected $hidden = ['root_password', 'ssh_key'];

    public static array $validationRules = [
        'name' => 'required|string|max:100',
        'description' => 'nullable|string',
        'owner_id' => 'required|exists:users,id',
        'lxc_node_id' => 'required|exists:lxc_nodes,id',
        'image' => 'required|string',
        'memory' => 'required|integer|min:16',
        'swap' => 'integer|min:0',
        'disk' => 'required|integer|min:128',
        'cpu_limit' => 'integer|min:0',
        'cpu_pinning' => 'nullable|string',
        'io_weight' => 'integer|between:10,1000',
    ];

    public function setRootPasswordAttribute($value): void
    {
        $this->attributes['root_password'] = $value ? encrypt($value) : $value;
    }

    public function getDecryptedRootPassword(): ?string
    {
        return $this->root_password ? decrypt($this->root_password) : null;
    }

    public function node(): BelongsTo
    {
        return $this->belongsTo(LxcNode::class, 'lxc_node_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(LxcContainerAllocation::class);
    }

    public function isRunning(): bool
    {
        return $this->status === self::STATUS_RUNNING;
    }
}
