<?php

namespace Hyperodactyl\Services\Hyperodactyl\Economy;

use Carbon\CarbonImmutable;
use Hyperodactyl\Models\User;
use Hyperodactyl\Models\HyperSetting;
use Hyperodactyl\Facades\Activity;
use Hyperodactyl\Models\HyperRewardClaim;
use Hyperodactyl\Models\HyperTransaction;
use Illuminate\Database\ConnectionInterface;
use Hyperodactyl\Exceptions\DisplayException;

class RewardService
{
    public const DEFAULT_DAILY_AMOUNT = 100;

    public function __construct(
        private ConnectionInterface $connection,
        private CoinService $coinService,
    ) {
    }

    /**
     * Attempt to claim the daily reward for a user, enforcing a strict 24 hour
     * cooldown between claims of the same kind.
     *
     * @throws DisplayException
     */
    public function claim(User $user, string $kind = HyperRewardClaim::KIND_DAILY): HyperTransaction
    {
        $cooldownHours = $kind === HyperRewardClaim::KIND_HOURLY ? 1 : 24;

        return $this->connection->transaction(function () use ($user, $kind, $cooldownHours) {
            $lastClaim = HyperRewardClaim::query()
                ->where('user_id', $user->id)
                ->where('kind', $kind)
                ->lockForUpdate()
                ->orderByDesc('claimed_at')
                ->first();

            if ($lastClaim && $lastClaim->claimed_at->diffInSeconds(now(), false) < $cooldownHours * 3600) {
                $availableAt = $lastClaim->claimed_at->addHours($cooldownHours);

                throw new DisplayException(
                    'You have already claimed this reward. It will be available again at ' . $availableAt->toDayDateTimeString() . '.'
                );
            }

            HyperRewardClaim::query()->create([
                'user_id' => $user->id,
                'kind' => $kind,
                'claimed_at' => now(),
            ]);

            $amount = $this->rewardAmount($kind);

            $transaction = $this->coinService->award(
                $user,
                $amount,
                HyperTransaction::TYPE_REWARD,
                ucfirst($kind) . ' login reward',
                ['kind' => $kind]
            );

            Activity::event('hyper:reward.claim')
                ->subject($user)
                ->property('kind', $kind)
                ->property('amount', $amount)
                ->log();

            return $transaction;
        });
    }

    /**
     * Returns the next time the given kind of reward may be claimed, or null
     * if it is available right now.
     */
    public function nextAvailableAt(User $user, string $kind = HyperRewardClaim::KIND_DAILY): ?CarbonImmutable
    {
        $cooldownHours = $kind === HyperRewardClaim::KIND_HOURLY ? 1 : 24;

        $lastClaim = HyperRewardClaim::query()
            ->where('user_id', $user->id)
            ->where('kind', $kind)
            ->orderByDesc('claimed_at')
            ->first();

        if (!$lastClaim) {
            return null;
        }

        $availableAt = CarbonImmutable::parse($lastClaim->claimed_at)->addHours($cooldownHours);

        return $availableAt->isPast() ? null : $availableAt;
    }

    private function rewardAmount(string $kind): int
    {
        $key = $kind === HyperRewardClaim::KIND_HOURLY ? 'hourly_reward_amount' : 'daily_reward_amount';
        $setting = HyperSetting::query()->where('key', $key)->first();

        if ($setting && is_numeric($setting->value)) {
            return (int) $setting->value;
        }

        return self::DEFAULT_DAILY_AMOUNT;
    }
}
