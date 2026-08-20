<?php

namespace Hyperodactyl\Http\Controllers\Api\Application\Hyper;

use Illuminate\Http\Request;
use Hyperodactyl\Models\HyperStoreItem;
use Hyperodactyl\Http\Controllers\Api\Application\ApplicationApiController;

class StoreItemController extends ApplicationApiController
{
    public function index(): array
    {
        $items = HyperStoreItem::query()->orderBy('id')->get();

        return [
            'object' => 'list',
            'data' => $items->map(fn (HyperStoreItem $item) => ['object' => HyperStoreItem::RESOURCE_NAME, 'attributes' => $item->toArray()]),
        ];
    }

    public function store(Request $request): array
    {
        $data = $request->validate(HyperStoreItem::$validationRules);
        $item = HyperStoreItem::query()->create($data);

        return ['object' => HyperStoreItem::RESOURCE_NAME, 'attributes' => $item->toArray()];
    }

    public function update(Request $request, HyperStoreItem $item): array
    {
        $rules = HyperStoreItem::$validationRules;
        $rules = array_map(fn ($rule) => 'sometimes|' . $rule, $rules);

        $data = $request->validate($rules);
        $item->update($data);

        return ['object' => HyperStoreItem::RESOURCE_NAME, 'attributes' => $item->fresh()->toArray()];
    }

    public function delete(HyperStoreItem $item): \Illuminate\Http\Response
    {
        $item->delete();

        return $this->returnNoContent();
    }
}
