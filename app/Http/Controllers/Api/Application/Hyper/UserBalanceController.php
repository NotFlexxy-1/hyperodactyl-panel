<?php

namespace Hyperodactyl\Http\Controllers\Api\Application\Hyper;

use Illuminate\Http\Request;
use Hyperodactyl\Models\User;
use Hyperodactyl\Models\HyperTransaction;
use Hyperodactyl\Http\Controllers\Api\Application\ApplicationApiController;
use Hyperodactyl\Services\Hyperodactyl\Economy\CoinService;

class UserBalanceController extends ApplicationApiController
{
    public function __construct(private CoinService $coinService)
    {
        parent::__construct();
    }

    public function index(Request $request): array
    {
        $users = User::query()
            ->select(['id', 'username', 'email', 'hyper_coins'])
            ->when($request->query('filter'), fn ($query, $filter) => $query->where('username', 'like', "%{$filter}%")->orWhere('email', 'like', "%{$filter}%"))
            ->orderBy('id')
            ->paginate(50);

        return [
            'object' => 'list',
            'data' => $users->map(fn (User $user) => [
                'object' => 'hyper_user_balance',
                'attributes' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'hyper_coins' => $user->hyper_coins,
                ],
            ]),
            'meta' => [
                'pagination' => [
                    'total' => $users->total(),
                    'current_page' => $users->currentPage(),
                    'total_pages' => $users->lastPage(),
                ],
            ],
        ];
    }

    public function adjust(Request $request, User $user): array
    {
        $data = $request->validate([
            'amount' => 'required|integer|not_in:0',
            'description' => 'nullable|string|max:255',
        ]);

        $transaction = $this->coinService->adjust($user, (int) $data['amount'], $data['description'] ?? 'Admin balance adjustment');

        return [
            'object' => HyperTransaction::RESOURCE_NAME,
            'attributes' => $transaction->toArray(),
        ];
    }
}
