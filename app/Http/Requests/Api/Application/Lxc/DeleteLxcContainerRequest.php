<?php

namespace Hyperodactyl\Http\Requests\Api\Application\Lxc;

use Hyperodactyl\Services\Acl\Api\AdminAcl;
use Hyperodactyl\Http\Requests\Api\Application\ApplicationApiRequest;

class DeleteLxcContainerRequest extends ApplicationApiRequest
{
    protected ?string $resource = AdminAcl::RESOURCE_LXC_CONTAINERS;

    protected int $permission = AdminAcl::WRITE;
}
