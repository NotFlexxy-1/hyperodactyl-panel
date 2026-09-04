<?php

namespace Hyperodactyl\Http\Requests\Api\Application\Servers\Databases;

use Hyperodactyl\Services\Acl\Api\AdminAcl;

class ServerDatabaseWriteRequest extends GetServerDatabasesRequest
{
    protected int $permission = AdminAcl::WRITE;
}
