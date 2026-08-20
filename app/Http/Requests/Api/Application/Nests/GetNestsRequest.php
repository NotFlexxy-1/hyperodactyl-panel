<?php

namespace Hyperodactyl\Http\Requests\Api\Application\Nests;

use Hyperodactyl\Services\Acl\Api\AdminAcl;
use Hyperodactyl\Http\Requests\Api\Application\ApplicationApiRequest;

class GetNestsRequest extends ApplicationApiRequest
{
    protected ?string $resource = AdminAcl::RESOURCE_NESTS;

    protected int $permission = AdminAcl::READ;
}
