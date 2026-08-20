<?php

namespace Hyperodactyl\Http\Requests\Api\Client\Servers\Schedules;

use Hyperodactyl\Models\Permission;
use Hyperodactyl\Http\Requests\Api\Client\ClientApiRequest;

class TriggerScheduleRequest extends ClientApiRequest
{
    public function permission(): string
    {
        return Permission::ACTION_SCHEDULE_UPDATE;
    }

    public function rules(): array
    {
        return [];
    }
}
