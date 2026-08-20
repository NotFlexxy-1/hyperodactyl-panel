<?php

namespace Hyperodactyl\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Hyperodactyl\Models\HyperTransaction.
 *
 * @property int $id
 * @property int $user_id
 * @property int $amount
 * @property string $type
 * @property string|null $description
 * @property array|null $meta
 * @property int $balance_after
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property User $user
 */
class HyperTransaction extends Model
{
    public const RESOURCE_NAME = 'hyper_transaction';

    public const TYPE_EARN = 'earn';
    public const TYPE_SPEND = 'spend';
    public const TYPE_ADMIN_GRANT = 'admin_grant';
    public const TYPE_REFUND = 'refund';
    public const TYPE_PURCHASE = 'purchase';
    public const TYPE_REWARD = 'reward';

    protected $table = 'hyper_transactions';

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'description',
        'meta',
        'balance_after',
    ];

    protected $casts = [
        'user_id' => 'int',
        'amount' => 'int',
        'meta' => 'array',
        'balance_after' => 'int',
    ];

    public static array $validationRules = [
        'user_id' => 'required|integer|exists:users,id',
        'amount' => 'required|integer',
        'type' => 'required|string|in:earn,spend,admin_grant,refund,purchase,reward',
        'description' => 'nullable|string|max:255',
        'meta' => 'nullable|array',
        'balance_after' => 'required|integer|min:0',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
