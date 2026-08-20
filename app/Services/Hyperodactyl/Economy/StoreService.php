<?php

namespace Hyperodactyl\Services\Hyperodactyl\Economy;

use Hyperodactyl\Models\User;
use Hyperodactyl\Models\Server;
use Hyperodactyl\Facades\Activity;
use Hyperodactyl\Models\HyperPurchase;
use Hyperodactyl\Models\HyperStoreItem;
use Hyperodactyl\Models\HyperTransaction;
use Illuminate\Database\ConnectionInterface;
use Hyperodactyl\Exceptions\DisplayException;
use Hyperodactyl\Services\Servers\BuildModificationService;

class StoreService
{
    /**
     * Resource keys that may be modified on a server via a store purchase, mapped
     * to the corresponding attribute understood by BuildModificationService.
     */
    private const RESOURCE_KEYS = [
        'memory', 'swap', 'disk', 'io', 'cpu', 'threads',
        'database_limit', 'allocation_limit', 'backup_limit',
    ];

    public function __construct(
        private ConnectionInterface $connection,
        private CoinService $coinService,
        private BuildModificationService $buildModificationService,
    ) {
    }

    /**
     * Purchase a store item on behalf of a user, applying any real effect the
     * item grants and recording the purchase.
     *
     * @throws DisplayException
     */
    public function purchase(User $user, HyperStoreItem $item, ?Server $server = null): HyperPurchase
    {
        if (!$item->enabled) {
            throw new DisplayException('This item is not currently available in the store.');
        }

        if (!is_null($item->stock) && $item->stock <= 0) {
            throw new DisplayException('This item is out of stock.');
        }

        $effect = $item->effect ?? [];
        $needsServer = $item->category === HyperStoreItem::CATEGORY_RESOURCE || $item->category === HyperStoreItem::CATEGORY_SERVER_SLOT;

        if ($needsServer) {
            if (is_null($server)) {
                throw new DisplayException('You must select a server to apply this item to.');
            }

            if ($server->owner_id !== $user->id) {
                throw new DisplayException('You do not own the selected server.');
            }
        }

        $purchase = $this->connection->transaction(function () use ($user, $item, $server, $needsServer) {
            // Lock the row to avoid a race condition depleting stock beyond zero.
            /** @var HyperStoreItem $locked */
            $locked = HyperStoreItem::query()->lockForUpdate()->findOrFail($item->id);

            if (!$locked->enabled) {
                throw new DisplayException('This item is not currently available in the store.');
            }

            if (!is_null($locked->stock)) {
                if ($locked->stock <= 0) {
                    throw new DisplayException('This item is out of stock.');
                }

                $locked->decrement('stock');
            }

            $this->coinService->deduct(
                $user,
                $locked->price,
                HyperTransaction::TYPE_PURCHASE,
                "Purchased {$locked->name}",
                ['item_id' => $locked->id]
            );

            $purchase = HyperPurchase::query()->create([
                'user_id' => $user->id,
                'item_id' => $locked->id,
                'server_id' => $needsServer ? $server->id : null,
                'price_paid' => $locked->price,
                'status' => 'completed',
            ]);

            if ($needsServer) {
                $this->applyResourceEffect($server, $locked->effect ?? []);
            }

            return $purchase;
        });

        Activity::event('hyper:store.purchase')
            ->subject($user, $item)
            ->property('price', $item->price)
            ->property('server_id', $purchase->server_id)
            ->log();

        return $purchase->fresh(['item', 'server']);
    }

    /**
     * Apply a resource-modifying effect to a server by delegating to the same
     * service that powers the admin build configuration screen.
     *
     * @throws DisplayException
     * @throws \Throwable
     */
    private function applyResourceEffect(Server $server, array $effect): void
    {
        $resource = $effect['resource'] ?? null;
        $amount = (int) ($effect['amount'] ?? 0);

        if (!$resource || !in_array($resource, self::RESOURCE_KEYS, true) || $amount <= 0) {
            return;
        }

        $current = $server->{$resource};
        $data = [$resource => $current + $amount];

        $this->buildModificationService->handle($server, $data);
    }
}
