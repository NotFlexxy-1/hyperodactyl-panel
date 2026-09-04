<?php

namespace Hyperodactyl\Http\Requests\Api\Client\Servers\Databases;

use Hyperodactyl\Models\Permission;
use Hyperodactyl\Contracts\Http\ClientPermissionsRequest;
use Hyperodactyl\Http\Requests\Api\Client\ClientApiRequest;

class GetDatabasesRequest extends ClientApiRequest implements ClientPermissionsRequest
{
    public function permission(): string
    {
        return Permission::ACTION_DATABASE_READ;
    }
}
