<?php

namespace Hyperodactyl\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Hyperodactyl\Models\HyperPurchase.
 *
 * @property int $id
 * @property int $user_id
 * @property int $item_id
 * @property int|null $server_id
 * @property int $price_paid
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property User $user
 * @property HyperStoreItem $item
 * @property Server|null $server
 */
class HyperPurchase extends Model
{
    public const RESOURCE_NAME = 'hyper_purchase';

    protected $table = 'hyper_purchases';

    protected $fillable = [
        'user_id',
        'item_id',
        'server_id',
        'price_paid',
        'status',
    ];

    protected $casts = [
        'user_id' => 'int',
        'item_id' => 'int',
        'server_id' => 'int',
        'price_paid' => 'int',
    ];

    public static array $validationRules = [
        'user_id' => 'required|integer|exists:users,id',
        'item_id' => 'required|integer|exists:hyper_store_items,id',
        'server_id' => 'nullable|integer|exists:servers,id',
        'price_paid' => 'required|integer|min:0',
        'status' => 'required|string|max:64',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(HyperStoreItem::class, 'item_id');
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
