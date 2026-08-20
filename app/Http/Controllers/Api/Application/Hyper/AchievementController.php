<?php

namespace Hyperodactyl\Http\Controllers\Api\Application\Hyper;

use Illuminate\Http\Request;
use Hyperodactyl\Models\HyperAchievement;
use Hyperodactyl\Http\Controllers\Api\Application\ApplicationApiController;

class AchievementController extends ApplicationApiController
{
    public function index(): array
    {
        $achievements = HyperAchievement::query()->orderBy('id')->get();

        return [
            'object' => 'list',
            'data' => $achievements->map(fn (HyperAchievement $achievement) => ['object' => HyperAchievement::RESOURCE_NAME, 'attributes' => $achievement->toArray()]),
        ];
    }

    public function store(Request $request): array
    {
        $data = $request->validate(HyperAchievement::$validationRules);
        $achievement = HyperAchievement::query()->create($data);

        return ['object' => HyperAchievement::RESOURCE_NAME, 'attributes' => $achievement->toArray()];
    }

    public function update(Request $request, HyperAchievement $achievement): array
    {
        $rules = HyperAchievement::$validationRules;
        $rules['key'] = 'required|string|max:191|unique:hyper_achievements,key,' . $achievement->id;
        $rules = array_map(fn ($rule) => 'sometimes|' . $rule, $rules);

        $data = $request->validate($rules);
        $achievement->update($data);

        return ['object' => HyperAchievement::RESOURCE_NAME, 'attributes' => $achievement->fresh()->toArray()];
    }

    public function delete(HyperAchievement $achievement): \Illuminate\Http\Response
    {
        $achievement->delete();

        return $this->returnNoContent();
    }
}
