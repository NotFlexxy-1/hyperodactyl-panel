<?php

namespace Hyperodactyl\Http\Requests\Api\Application\Nests\Eggs;

use Hyperodactyl\Services\Acl\Api\AdminAcl;
use Hyperodactyl\Http\Requests\Api\Application\ApplicationApiRequest;

class GetEggRequest extends ApplicationApiRequest
{
    protected ?string $resource = AdminAcl::RESOURCE_EGGS;

    protected int $permission = AdminAcl::READ;
}
