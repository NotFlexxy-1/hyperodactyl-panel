<?php

namespace Hyperodactyl\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Hyperodactyl\Models\HyperUserAchievement.
 *
 * @property int $id
 * @property int $user_id
 * @property int $achievement_id
 * @property \Illuminate\Support\Carbon $unlocked_at
 * @property User $user
 * @property HyperAchievement $achievement
 */
class HyperUserAchievement extends Model
{
    public const RESOURCE_NAME = 'hyper_user_achievement';

    public $timestamps = false;

    protected $table = 'hyper_user_achievements';

    protected $fillable = [
        'user_id',
        'achievement_id',
        'unlocked_at',
    ];

    protected $casts = [
        'user_id' => 'int',
        'achievement_id' => 'int',
        'unlocked_at' => 'datetime',
    ];

    public static array $validationRules = [
        'user_id' => 'required|integer|exists:users,id',
        'achievement_id' => 'required|integer|exists:hyper_achievements,id',
        'unlocked_at' => 'required|date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(HyperAchievement::class, 'achievement_id');
    }
}
