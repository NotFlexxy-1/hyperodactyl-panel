<?php

namespace Hyperodactyl\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Hyperodactyl\Models\HyperAchievement.
 *
 * @property int $id
 * @property string $key
 * @property string $name
 * @property string|null $description
 * @property string|null $icon
 * @property int $coin_reward
 * @property array $criteria
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class HyperAchievement extends Model
{
    public const RESOURCE_NAME = 'hyper_achievement';

    protected $table = 'hyper_achievements';

    protected $fillable = [
        'key',
        'name',
        'description',
        'icon',
        'coin_reward',
        'criteria',
    ];

    protected $casts = [
        'coin_reward' => 'int',
        'criteria' => 'array',
    ];

    public static array $validationRules = [
        'key' => 'required|string|max:191|unique:hyper_achievements,key',
        'name' => 'required|string|max:191',
        'description' => 'nullable|string|max:1000',
        'icon' => 'nullable|string|max:191',
        'coin_reward' => 'required|integer|min:0',
        'criteria' => 'required|array',
    ];

    public function unlocks(): HasMany
    {
        return $this->hasMany(HyperUserAchievement::class, 'achievement_id');
    }
}
