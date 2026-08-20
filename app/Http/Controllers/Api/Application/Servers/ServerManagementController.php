<?php

namespace Hyperodactyl\Http\Controllers\Api\Application\Servers;

use Illuminate\Http\Response;
use Hyperodactyl\Models\Server;
use Hyperodactyl\Services\Servers\SuspensionService;
use Hyperodactyl\Services\Servers\ReinstallServerService;
use Hyperodactyl\Http\Requests\Api\Application\Servers\ServerWriteRequest;
use Hyperodactyl\Http\Controllers\Api\Application\ApplicationApiController;

class ServerManagementController extends ApplicationApiController
{
    /**
     * ServerManagementController constructor.
     */
    public function __construct(
        private ReinstallServerService $reinstallServerService,
        private SuspensionService $suspensionService,
    ) {
        parent::__construct();
    }

    /**
     * Suspend a server on the Panel.
     *
     * @throws \Throwable
     */
    public function suspend(ServerWriteRequest $request, Server $server): Response
    {
        $this->suspensionService->toggle($server);

        return $this->returnNoContent();
    }

    /**
     * Unsuspend a server on the Panel.
     *
     * @throws \Throwable
     */
    public function unsuspend(ServerWriteRequest $request, Server $server): Response
    {
        $this->suspensionService->toggle($server, SuspensionService::ACTION_UNSUSPEND);

        return $this->returnNoContent();
    }

    /**
     * Mark a server as needing to be reinstalled.
     *
     * @throws \Hyperodactyl\Exceptions\DisplayException
     * @throws \Hyperodactyl\Exceptions\Model\DataValidationException
     * @throws \Hyperodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function reinstall(ServerWriteRequest $request, Server $server): Response
    {
        $this->reinstallServerService->handle($server);

        return $this->returnNoContent();
    }
}
