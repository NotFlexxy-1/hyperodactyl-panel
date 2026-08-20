<?php

namespace Hyperodactyl\Http\Requests\Api\Application\Lxc;

use Hyperodactyl\Models\LxcContainer;
use Hyperodactyl\Services\Acl\Api\AdminAcl;
use Hyperodactyl\Http\Requests\Api\Application\ApplicationApiRequest;

class StoreLxcContainerRequest extends ApplicationApiRequest
{
    protected ?string $resource = AdminAcl::RESOURCE_LXC_CONTAINERS;

    protected int $permission = AdminAcl::WRITE;

    public function rules(?array $rules = null): array
    {
        return $rules ?? LxcContainer::$validationRules;
    }
}
