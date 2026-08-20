<?php

namespace Hyperodactyl\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string|null $description
 * @property string $fqdn
 * @property string $scheme
 * @property int $port
 * @property string $driver
 * @property string $api_token
 * @property string|null $api_secret
 * @property bool $tls_verify
 * @property string|null $proxmox_node
 * @property string $storage_pool
 * @property string $network_bridge
 * @property bool $maintenance_mode
 * @property int $memory
 * @property int $memory_overallocate
 * @property int $disk
 * @property int $disk_overallocate
 * @property int $cpu
 * @property int $cpu_overallocate
 */
class LxcNode extends Model
{
    public const DRIVER_LXD = 'lxd';
    public const DRIVER_PROXMOX = 'proxmox';

    protected $table = 'lxc_nodes';

    protected $casts = [
        'tls_verify' => 'bool',
        'maintenance_mode' => 'bool',
        'memory' => 'int',
        'memory_overallocate' => 'int',
        'disk' => 'int',
        'disk_overallocate' => 'int',
        'cpu' => 'int',
        'cpu_overallocate' => 'int',
        'port' => 'int',
    ];

    protected $hidden = ['api_token', 'api_secret'];

    protected $attributes = [
        'scheme' => 'https',
        'port' => 8443,
        'driver' => self::DRIVER_LXD,
        'tls_verify' => true,
        'maintenance_mode' => false,
        'storage_pool' => 'default',
        'network_bridge' => 'lxdbr0',
    ];

    public static array $validationRules = [
        'name' => 'required|string|max:100',
        'description' => 'nullable|string',
        'fqdn' => 'required|string',
        'scheme' => 'required|in:http,https',
        'port' => 'required|integer|between:1,65535',
        'driver' => 'required|in:lxd,proxmox',
        'api_token' => 'required|string',
        'api_secret' => 'nullable|string',
        'tls_verify' => 'boolean',
        'proxmox_node' => 'nullable|string|required_if:driver,proxmox',
        'storage_pool' => 'required|string',
        'network_bridge' => 'required|string',
        'maintenance_mode' => 'boolean',
        'memory' => 'required|integer|min:0',
        'memory_overallocate' => 'integer|min:-1',
        'disk' => 'required|integer|min:0',
        'disk_overallocate' => 'integer|min:-1',
        'cpu' => 'required|integer|min:0',
        'cpu_overallocate' => 'integer|min:-1',
    ];

    public function getConnectionAddress(): string
    {
        return sprintf('%s://%s:%s', $this->scheme, $this->fqdn, $this->port);
    }

    public function getDecryptedToken(): string
    {
        return decrypt($this->api_token);
    }

    public function getDecryptedSecret(): ?string
    {
        return $this->api_secret ? decrypt($this->api_secret) : null;
    }

    public function setApiTokenAttribute($value): void
    {
        $this->attributes['api_token'] = $value ? encrypt($value) : $value;
    }

    public function setApiSecretAttribute($value): void
    {
        $this->attributes['api_secret'] = $value ? encrypt($value) : $value;
    }

    public function containers(): HasMany
    {
        return $this->hasMany(LxcContainer::class);
    }
}
