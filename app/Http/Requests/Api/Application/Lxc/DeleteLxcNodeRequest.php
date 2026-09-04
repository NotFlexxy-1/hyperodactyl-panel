<?php

namespace Hyperodactyl\Http\Requests\Api\Application\Lxc;

use Hyperodactyl\Services\Acl\Api\AdminAcl;
use Hyperodactyl\Http\Requests\Api\Application\ApplicationApiRequest;

class DeleteLxcNodeRequest extends ApplicationApiRequest
{
    protected ?string $resource = AdminAcl::RESOURCE_LXC_NODES;

    protected int $permission = AdminAcl::WRITE;
}
