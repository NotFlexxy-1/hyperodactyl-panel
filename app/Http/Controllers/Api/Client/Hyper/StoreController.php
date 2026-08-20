<?php

namespace Hyperodactyl\Http\Controllers\Api\Client\Hyper;

use Illuminate\Http\Request;
use Hyperodactyl\Models\Server;
use Hyperodactyl\Models\HyperStoreItem;
use Hyperodactyl\Models\HyperPurchase;
use Illuminate\Http\JsonResponse;
use Hyperodactyl\Exceptions\DisplayException;
use Hyperodactyl\Http\Controllers\Api\Client\ClientApiController;
use Hyperodactyl\Services\Hyperodactyl\Economy\StoreService;
use Hyperodactyl\Services\Hyperodactyl\Economy\AchievementService;

class StoreController extends ClientApiController
{
    public function __construct(
        private StoreService $storeService,
        private AchievementService $achievementService,
    ) {
        parent::__construct();
    }

    /**
     * List all enabled store items available for purchase.
     */
    public function index(): array
    {
        $items = HyperStoreItem::query()
            ->where('enabled', true)
            ->orderBy('category')
            ->orderBy('price')
            ->get();

        return [
            'object' => 'list',
            'data' => $items->map(fn (HyperStoreItem $item) => [
                'object' => HyperStoreItem::RESOURCE_NAME,
                'attributes' => $item->toArray(),
            ]),
        ];
    }

    /**
     * List the authenticated user's purchase history.
     */
    public function history(Request $request): array
    {
        $purchases = HyperPurchase::query()
            ->with(['item', 'server'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->paginate(25);

        return [
            'object' => 'list',
            'data' => $purchases->map(fn (HyperPurchase $purchase) => [
                'object' => HyperPurchase::RESOURCE_NAME,
                'attributes' => array_merge($purchase->toArray(), [
                    'item' => $purchase->item,
                    'server' => $purchase->server,
                ]),
            ]),
        ];
    }

    /**
     * Purchase a store item, optionally applying its effect to a server owned
     * by the requesting user.
     *
     * @throws DisplayException
     */
    public function purchase(Request $request, HyperStoreItem $item): array
    {
        $data = $request->validate([
            'server' => 'nullable|string|exists:servers,uuid',
        ]);

        $server = null;
        if (!empty($data['server'])) {
            $server = Server::query()->where('uuid', $data['server'])->firstOrFail();
        }

        $purchase = $this->storeService->purchase($request->user(), $item, $server);

        $this->achievementService->evaluate($request->user());

        return [
            'object' => HyperPurchase::RESOURCE_NAME,
            'attributes' => array_merge($purchase->toArray(), [
                'item' => $purchase->item,
                'server' => $purchase->server,
            ]),
            'balance' => $request->user()->fresh()->hyper_coins,
        ];
    }
}
