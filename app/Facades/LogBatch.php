<?php

namespace Hyperodactyl\Facades;

use Illuminate\Support\Facades\Facade;
use Hyperodactyl\Services\Activity\ActivityLogBatchService;

class LogBatch extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ActivityLogBatchService::class;
    }
}
