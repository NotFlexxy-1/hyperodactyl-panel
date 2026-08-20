<?php

namespace Hyperodactyl\Facades;

use Illuminate\Support\Facades\Facade;
use Hyperodactyl\Services\Activity\ActivityLogService;

class Activity extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ActivityLogService::class;
    }
}
