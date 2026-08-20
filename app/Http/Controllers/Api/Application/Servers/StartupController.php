<?php

namespace Hyperodactyl\Http\Controllers\Api\Application\Servers;

use Hyperodactyl\Models\User;
use Hyperodactyl\Models\Server;
use Hyperodactyl\Services\Servers\StartupModificationService;
use Hyperodactyl\Transformers\Api\Application\ServerTransformer;
use Hyperodactyl\Http\Controllers\Api\Application\ApplicationApiController;
use Hyperodactyl\Http\Requests\Api\Application\Servers\UpdateServerStartupRequest;

class StartupController extends ApplicationApiController
{
    /**
     * StartupController constructor.
     */
    public function __construct(private StartupModificationService $modificationService)
    {
        parent::__construct();
    }

    /**
     * Update the startup and environment settings for a specific server.
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Hyperodactyl\Exceptions\Http\Connection\DaemonConnectionException
     * @throws \Hyperodactyl\Exceptions\Model\DataValidationException
     * @throws \Hyperodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function index(UpdateServerStartupRequest $request, Server $server): array
    {
        $server = $this->modificationService
            ->setUserLevel(User::USER_LEVEL_ADMIN)
            ->handle($server, $request->validated());

        return $this->fractal->item($server)
            ->transformWith($this->getTransformer(ServerTransformer::class))
            ->toArray();
    }
}
