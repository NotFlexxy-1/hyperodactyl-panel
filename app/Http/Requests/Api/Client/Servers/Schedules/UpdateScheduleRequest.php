<?php

namespace Hyperodactyl\Http\Requests\Api\Client\Servers\Schedules;

use Hyperodactyl\Models\Permission;

class UpdateScheduleRequest extends StoreScheduleRequest
{
    public function permission(): string
    {
        return Permission::ACTION_SCHEDULE_UPDATE;
    }
}
