<?php

namespace Hyperodactyl\Http\Requests\Admin;

use Hyperodactyl\Models\Mount;

class MountFormRequest extends AdminFormRequest
{
    /**
     * Set up the validation rules to use for these requests.
     */
    public function rules(): array
    {
        if ($this->method() === 'PATCH') {
            return Mount::getRulesForUpdate($this->route()->parameter('mount')->id); // @phpstan-ignore property.nonObject
        }

        return Mount::getRules();
    }
}
