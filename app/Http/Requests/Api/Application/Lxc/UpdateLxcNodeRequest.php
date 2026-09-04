<?php

namespace Hyperodactyl\Http\Requests\Api\Application\Lxc;

class UpdateLxcNodeRequest extends StoreLxcNodeRequest
{
    public function rules(?array $rules = null): array
    {
        $rules = parent::rules($rules);
        $rules['api_token'] = 'sometimes|string';

        return $rules;
    }
}
