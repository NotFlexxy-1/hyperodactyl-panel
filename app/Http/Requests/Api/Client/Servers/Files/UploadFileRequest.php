<?php

namespace Hyperodactyl\Http\Requests\Api\Client\Servers\Files;

use Hyperodactyl\Models\Permission;
use Hyperodactyl\Http\Requests\Api\Client\ClientApiRequest;

class UploadFileRequest extends ClientApiRequest
{
    public function permission(): string
    {
        return Permission::ACTION_FILE_CREATE;
    }
}
