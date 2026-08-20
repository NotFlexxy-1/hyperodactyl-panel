<?php

namespace Hyperodactyl\Http\Requests\Api\Application\Locations;

use Hyperodactyl\Services\Acl\Api\AdminAcl;
use Hyperodactyl\Http\Requests\Api\Application\ApplicationApiRequest;

class DeleteLocationRequest extends ApplicationApiRequest
{
    protected ?string $resource = AdminAcl::RESOURCE_LOCATIONS;

    protected int $permission = AdminAcl::WRITE;
}
