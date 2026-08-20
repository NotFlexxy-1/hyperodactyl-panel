<?php

namespace Hyperodactyl\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Hyperodactyl\Models\HyperStoreItem.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $category
 * @property string|null $icon
 * @property int $price
 * @property array|null $effect
 * @property bool $enabled
 * @property int|null $stock
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class HyperStoreItem extends Model
{
    public const RESOURCE_NAME = 'hyper_store_item';

    public const CATEGORY_RESOURCE = 'resource';
    public const CATEGORY_SERVER_SLOT = 'server_slot';
    public const CATEGORY_COSMETIC = 'cosmetic';
    public const CATEGORY_OTHER = 'other';

    protected $table = 'hyper_store_items';

    protected $fillable = [
        'name',
        'description',
        'category',
        'icon',
        'price',
        'effect',
        'enabled',
        'stock',
    ];

    protected $casts = [
        'price' => 'int',
        'effect' => 'array',
        'enabled' => 'bool',
        'stock' => 'int',
    ];

    public static array $validationRules = [
        'name' => 'required|string|max:191',
        'description' => 'nullable|string|max:1000',
        'category' => 'required|string|in:resource,server_slot,cosmetic,other',
        'icon' => 'nullable|string|max:191',
        'price' => 'required|integer|min:0',
        'effect' => 'nullable|array',
        'enabled' => 'boolean',
        'stock' => 'nullable|integer|min:0',
    ];

    public function purchases(): HasMany
    {
        return $this->hasMany(HyperPurchase::class, 'item_id');
    }
}
