<?php

namespace Hyperodactyl\Http\Requests\Api\Client\Servers\Files;

use Hyperodactyl\Models\Permission;
use Hyperodactyl\Contracts\Http\ClientPermissionsRequest;
use Hyperodactyl\Http\Requests\Api\Client\ClientApiRequest;

class CopyFileRequest extends ClientApiRequest implements ClientPermissionsRequest
{
    public function permission(): string
    {
        return Permission::ACTION_FILE_CREATE;
    }

    public function rules(): array
    {
        return [
            'location' => 'required|string',
        ];
    }
}
