<?php

namespace Hyperodactyl\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Hyperodactyl\Models\HyperRewardClaim.
 *
 * @property int $id
 * @property int $user_id
 * @property string $kind
 * @property \Illuminate\Support\Carbon $claimed_at
 * @property User $user
 */
class HyperRewardClaim extends Model
{
    public const RESOURCE_NAME = 'hyper_reward_claim';

    public const KIND_DAILY = 'daily';
    public const KIND_HOURLY = 'hourly';

    protected $table = 'hyper_reward_claims';

    protected $fillable = [
        'user_id',
        'kind',
        'claimed_at',
    ];

    protected $casts = [
        'user_id' => 'int',
        'claimed_at' => 'datetime',
    ];

    public static array $validationRules = [
        'user_id' => 'required|integer|exists:users,id',
        'kind' => 'required|string|in:daily,hourly',
        'claimed_at' => 'required|date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
