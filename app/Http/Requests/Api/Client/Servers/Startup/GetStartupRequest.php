<?php

namespace Hyperodactyl\Http\Requests\Api\Client\Servers\Startup;

use Hyperodactyl\Models\Permission;
use Hyperodactyl\Http\Requests\Api\Client\ClientApiRequest;

class GetStartupRequest extends ClientApiRequest
{
    public function permission(): string
    {
        return Permission::ACTION_STARTUP_READ;
    }
}
