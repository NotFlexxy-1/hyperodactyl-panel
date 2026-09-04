<?php

namespace Hyperodactyl\Http\Requests\Api\Client\Servers\Subusers;

use Hyperodactyl\Models\Permission;

class DeleteSubuserRequest extends SubuserRequest
{
    public function permission(): string
    {
        return Permission::ACTION_USER_DELETE;
    }
}
