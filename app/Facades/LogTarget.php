<?php

namespace Hyperodactyl\Facades;

use Illuminate\Support\Facades\Facade;
use Hyperodactyl\Services\Activity\ActivityLogTargetableService;

/**
 * @mixin \Hyperodactyl\Services\Activity\ActivityLogTargetableService
 */
class LogTarget extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ActivityLogTargetableService::class;
    }
}
