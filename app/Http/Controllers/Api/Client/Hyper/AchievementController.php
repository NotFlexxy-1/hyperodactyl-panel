<?php

namespace Hyperodactyl\Http\Controllers\Api\Client\Hyper;

use Illuminate\Http\Request;
use Hyperodactyl\Models\HyperAchievement;
use Hyperodactyl\Models\HyperUserAchievement;
use Hyperodactyl\Http\Controllers\Api\Client\ClientApiController;
use Hyperodactyl\Services\Hyperodactyl\Economy\AchievementService;

class AchievementController extends ClientApiController
{
    public function __construct(private AchievementService $achievementService)
    {
        parent::__construct();
    }

    /**
     * List all achievements alongside the authenticated user's unlock state,
     * re-evaluating achievements against live data first.
     */
    public function index(Request $request): array
    {
        $user = $request->user();

        $this->achievementService->evaluate($user);

        $unlocked = HyperUserAchievement::query()
            ->where('user_id', $user->id)
            ->pluck('unlocked_at', 'achievement_id');

        $achievements = HyperAchievement::query()->orderBy('id')->get();

        return [
            'object' => 'list',
            'data' => $achievements->map(fn (HyperAchievement $achievement) => [
                'object' => HyperAchievement::RESOURCE_NAME,
                'attributes' => array_merge($achievement->toArray(), [
                    'unlocked' => $unlocked->has($achievement->id),
                    'unlocked_at' => $unlocked->get($achievement->id),
                ]),
            ]),
        ];
    }
}
