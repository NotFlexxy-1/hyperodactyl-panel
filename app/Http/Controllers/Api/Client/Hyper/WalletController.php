<?php

namespace Hyperodactyl\Http\Controllers\Api\Client\Hyper;

use Illuminate\Http\Request;
use Hyperodactyl\Models\HyperTransaction;
use Hyperodactyl\Http\Controllers\Api\Client\ClientApiController;

class WalletController extends ClientApiController
{
    /**
     * Return the authenticated user's Hyper Coin balance and recent transactions.
     */
    public function index(Request $request): array
    {
        $user = $request->user();

        $perPage = min((int) ($request->query('per_page') ?? 25), 100);

        $transactions = HyperTransaction::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->paginate($perPage);

        return [
            'object' => 'hyper_wallet',
            'attributes' => [
                'balance' => (int) $user->hyper_coins,
            ],
            'transactions' => $transactions->map(fn (HyperTransaction $transaction) => [
                'object' => HyperTransaction::RESOURCE_NAME,
                'attributes' => $transaction->toArray(),
            ]),
            'meta' => [
                'pagination' => [
                    'total' => $transactions->total(),
                    'count' => $transactions->count(),
                    'per_page' => $transactions->perPage(),
                    'current_page' => $transactions->currentPage(),
                    'total_pages' => $transactions->lastPage(),
                ],
            ],
        ];
    }
}
