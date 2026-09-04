<?php

namespace Hyperodactyl\Http\Requests\Api\Client\Servers\Schedules;

use Hyperodactyl\Models\Permission;

class DeleteScheduleRequest extends ViewScheduleRequest
{
    public function permission(): string
    {
        return Permission::ACTION_SCHEDULE_DELETE;
    }
}
