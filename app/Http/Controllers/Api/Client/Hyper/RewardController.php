<?php

namespace Hyperodactyl\Http\Controllers\Api\Client\Hyper;

use Illuminate\Http\Request;
use Hyperodactyl\Models\HyperRewardClaim;
use Hyperodactyl\Exceptions\DisplayException;
use Hyperodactyl\Http\Controllers\Api\Client\ClientApiController;
use Hyperodactyl\Services\Hyperodactyl\Economy\RewardService;

class RewardController extends ClientApiController
{
    public function __construct(private RewardService $rewardService)
    {
        parent::__construct();
    }

    /**
     * Return the reward claim status for the authenticated user.
     */
    public function index(Request $request): array
    {
        $user = $request->user();

        return [
            'object' => 'hyper_reward_status',
            'attributes' => [
                'daily_available_at' => $this->rewardService->nextAvailableAt($user, HyperRewardClaim::KIND_DAILY),
                'hourly_available_at' => $this->rewardService->nextAvailableAt($user, HyperRewardClaim::KIND_HOURLY),
            ],
        ];
    }

    /**
     * Claim the daily (or hourly) login reward.
     *
     * @throws DisplayException
     */
    public function claim(Request $request): array
    {
        $data = $request->validate([
            'kind' => 'sometimes|string|in:daily,hourly',
        ]);

        $transaction = $this->rewardService->claim($request->user(), $data['kind'] ?? HyperRewardClaim::KIND_DAILY);

        return [
            'object' => 'hyper_transaction',
            'attributes' => $transaction->toArray(),
        ];
    }
}
