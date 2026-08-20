<?php

namespace Hyperodactyl\Http\Requests\Api\Application\Allocations;

use Hyperodactyl\Services\Acl\Api\AdminAcl;
use Hyperodactyl\Http\Requests\Api\Application\ApplicationApiRequest;

class GetAllocationsRequest extends ApplicationApiRequest
{
    protected ?string $resource = AdminAcl::RESOURCE_ALLOCATIONS;

    protected int $permission = AdminAcl::READ;
}
