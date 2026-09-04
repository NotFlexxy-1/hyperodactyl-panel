<?php

namespace Hyperodactyl\Http\Requests\Api\Application\Lxc;

use Hyperodactyl\Models\LxcContainer;

class UpdateLxcContainerRequest extends StoreLxcContainerRequest
{
    public function rules(?array $rules = null): array
    {
        $rules = LxcContainer::$validationRules;
        $rules['name'] = 'sometimes|string|max:100';
        $rules['owner_id'] = 'sometimes|exists:users,id';
        $rules['lxc_node_id'] = 'sometimes|exists:lxc_nodes,id';
        $rules['image'] = 'sometimes|string';
        $rules['memory'] = 'sometimes|integer|min:16';
        $rules['disk'] = 'sometimes|integer|min:128';

        return $rules;
    }
}
