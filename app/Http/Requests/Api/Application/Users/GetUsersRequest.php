<?php

namespace Hyperodactyl\Http\Requests\Api\Application\Users;

use Hyperodactyl\Services\Acl\Api\AdminAcl as Acl;
use Hyperodactyl\Http\Requests\Api\Application\ApplicationApiRequest;

class GetUsersRequest extends ApplicationApiRequest
{
    protected ?string $resource = Acl::RESOURCE_USERS;

    protected int $permission = Acl::READ;
}
