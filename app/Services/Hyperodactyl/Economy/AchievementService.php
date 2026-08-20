<?php

namespace Hyperodactyl\Services\Hyperodactyl\Economy;

use Hyperodactyl\Models\User;
use Hyperodactyl\Facades\Activity;
use Hyperodactyl\Models\HyperPurchase;
use Hyperodactyl\Models\HyperAchievement;
use Hyperodactyl\Models\HyperTransaction;
use Hyperodactyl\Models\HyperUserAchievement;
use Illuminate\Database\ConnectionInterface;

class AchievementService
{
    public function __construct(
        private ConnectionInterface $connection,
        private CoinService $coinService,
    ) {
    }

    /**
     * Evaluate every achievement against the real, current state of the given
     * user's account and unlock any that now qualify. Returns the achievements
     * that were newly unlocked during this evaluation.
     *
     * @return \Illuminate\Support\Collection<int, HyperAchievement>
     */
    public function evaluate(User $user): \Illuminate\Support\Collection
    {
        $unlockedIds = HyperUserAchievement::query()
            ->where('user_id', $user->id)
            ->pluck('achievement_id')
            ->all();

        $metrics = $this->buildMetrics($user);

        $newlyUnlocked = new \Illuminate\Support\Collection();

        HyperAchievement::query()
            ->whereNotIn('id', $unlockedIds ?: [0])
            ->get()
            ->each(function (HyperAchievement $achievement) use ($user, $metrics, &$newlyUnlocked) {
                if ($this->meetsCriteria($achievement->criteria ?? [], $metrics)) {
                    $this->unlock($user, $achievement);
                    $newlyUnlocked->push($achievement);
                }
            });

        return $newlyUnlocked;
    }

    /**
     * Idempotently unlock a single achievement for a user and award its coins.
     */
    public function unlock(User $user, HyperAchievement $achievement): ?HyperUserAchievement
    {
        return $this->connection->transaction(function () use ($user, $achievement) {
            $existing = HyperUserAchievement::query()
                ->where('user_id', $user->id)
                ->where('achievement_id', $achievement->id)
                ->first();

            if ($existing) {
                return null;
            }

            $unlock = HyperUserAchievement::query()->create([
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
                'unlocked_at' => now(),
            ]);

            if ($achievement->coin_reward > 0) {
                $this->coinService->award(
                    $user,
                    $achievement->coin_reward,
                    HyperTransaction::TYPE_EARN,
                    "Achievement unlocked: {$achievement->name}",
                    ['achievement_id' => $achievement->id]
                );
            }

            Activity::event('hyper:achievement.unlock')
                ->subject($user, $achievement)
                ->property('achievement', $achievement->key)
                ->log();

            return $unlock;
        });
    }

    /**
     * Gather the real metrics used to evaluate achievement criteria.
     */
    private function buildMetrics(User $user): array
    {
        $serversOwned = $user->servers()->count();
        $backupsCount = \Hyperodactyl\Models\Backup::query()
            ->whereIn('server_id', $user->servers()->pluck('id'))
            ->count();
        $accountAgeDays = $user->created_at ? $user->created_at->diffInDays(now()) : 0;
        $coinsSpent = HyperPurchase::query()->where('user_id', $user->id)->sum('price_paid');

        return [
            'servers_owned' => (int) $serversOwned,
            'backups_count' => (int) $backupsCount,
            'account_age_days' => (int) $accountAgeDays,
            'coins_spent' => (int) $coinsSpent,
            'coins_balance' => (int) $user->hyper_coins,
        ];
    }

    /**
     * Supports either a single condition:
     *   {"type": "servers_owned", "min": 1}
     * or a combined "all" list of conditions (AND logic):
     *   {"all": [{"type": "servers_owned", "min": 1}, {"type": "backups_count", "min": 5}]}
     */
    private function meetsCriteria(array $criteria, array $metrics): bool
    {
        if (isset($criteria['all']) && is_array($criteria['all'])) {
            foreach ($criteria['all'] as $condition) {
                if (!$this->meetsCondition($condition, $metrics)) {
                    return false;
                }
            }

            return true;
        }

        return $this->meetsCondition($criteria, $metrics);
    }

    private function meetsCondition(array $condition, array $metrics): bool
    {
        $type = $condition['type'] ?? null;

        if (!$type || !array_key_exists($type, $metrics)) {
            return false;
        }

        $value = $metrics[$type];

        if (array_key_exists('min', $condition) && $value < $condition['min']) {
            return false;
        }

        if (array_key_exists('max', $condition) && $value > $condition['max']) {
            return false;
        }

        if (array_key_exists('equals', $condition) && $value != $condition['equals']) {
            return false;
        }

        return true;
    }
}
