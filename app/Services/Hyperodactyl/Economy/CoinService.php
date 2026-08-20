<?php

namespace Hyperodactyl\Services\Hyperodactyl\Economy;

use Hyperodactyl\Models\User;
use Hyperodactyl\Facades\Activity;
use Hyperodactyl\Models\HyperTransaction;
use Illuminate\Database\ConnectionInterface;
use Hyperodactyl\Exceptions\DisplayException;

class CoinService
{
    public function __construct(private ConnectionInterface $connection)
    {
    }

    /**
     * Award coins to a user. Returns the resulting transaction.
     */
    public function award(User $user, int $amount, string $type, ?string $description = null, array $meta = []): HyperTransaction
    {
        if ($amount <= 0) {
            throw new DisplayException('Amount to award must be a positive integer.');
        }

        return $this->connection->transaction(function () use ($user, $amount, $type, $description, $meta) {
            /** @var User $user */
            $user = User::query()->lockForUpdate()->findOrFail($user->id);

            $balance = $user->hyper_coins + $amount;
            $user->forceFill(['hyper_coins' => $balance])->saveOrFail();

            return HyperTransaction::query()->create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => $type,
                'description' => $description,
                'meta' => $meta,
                'balance_after' => $balance,
            ]);
        });
    }

    /**
     * Deduct coins from a user, throwing if the user does not have a sufficient
     * balance to cover the deduction.
     *
     * @throws DisplayException
     */
    public function deduct(User $user, int $amount, string $type, ?string $description = null, array $meta = []): HyperTransaction
    {
        if ($amount <= 0) {
            throw new DisplayException('Amount to deduct must be a positive integer.');
        }

        return $this->connection->transaction(function () use ($user, $amount, $type, $description, $meta) {
            /** @var User $user */
            $user = User::query()->lockForUpdate()->findOrFail($user->id);

            if ($user->hyper_coins < $amount) {
                throw new DisplayException('You do not have enough Hyper Coins to complete this action.');
            }

            $balance = $user->hyper_coins - $amount;
            $user->forceFill(['hyper_coins' => $balance])->saveOrFail();

            return HyperTransaction::query()->create([
                'user_id' => $user->id,
                'amount' => -$amount,
                'type' => $type,
                'description' => $description,
                'meta' => $meta,
                'balance_after' => $balance,
            ]);
        });
    }

    /**
     * Admin adjustment — can be positive or negative.
     *
     * @throws DisplayException
     */
    public function adjust(User $user, int $amount, ?string $description = null, array $meta = []): HyperTransaction
    {
        if ($amount === 0) {
            throw new DisplayException('Adjustment amount cannot be zero.');
        }

        $transaction = $amount > 0
            ? $this->award($user, $amount, HyperTransaction::TYPE_ADMIN_GRANT, $description, $meta)
            : $this->deduct($user, abs($amount), HyperTransaction::TYPE_ADMIN_GRANT, $description, $meta);

        Activity::event('hyper:coins.admin-adjust')
            ->subject($user)
            ->property('amount', $amount)
            ->property('description', $description)
            ->log();

        return $transaction;
    }

    public function balance(User $user): int
    {
        return (int) User::query()->findOrFail($user->id)->hyper_coins;
    }
}
