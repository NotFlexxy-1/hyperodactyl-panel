<?php

namespace Hyperodactyl\Http\Controllers\Api\Application\Servers;

use Hyperodactyl\Models\Server;
use Hyperodactyl\Services\Servers\BuildModificationService;
use Hyperodactyl\Services\Servers\DetailsModificationService;
use Hyperodactyl\Transformers\Api\Application\ServerTransformer;
use Hyperodactyl\Http\Controllers\Api\Application\ApplicationApiController;
use Hyperodactyl\Http\Requests\Api\Application\Servers\UpdateServerDetailsRequest;
use Hyperodactyl\Http\Requests\Api\Application\Servers\UpdateServerBuildConfigurationRequest;

class ServerDetailsController extends ApplicationApiController
{
    /**
     * ServerDetailsController constructor.
     */
    public function __construct(
        private BuildModificationService $buildModificationService,
        private DetailsModificationService $detailsModificationService,
    ) {
        parent::__construct();
    }

    /**
     * Update the details for a specific server.
     *
     * @throws \Hyperodactyl\Exceptions\DisplayException
     * @throws \Hyperodactyl\Exceptions\Model\DataValidationException
     * @throws \Hyperodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function details(UpdateServerDetailsRequest $request, Server $server): array
    {
        $updated = $this->detailsModificationService->returnUpdatedModel()->handle(
            $server,
            $request->validated()
        );

        return $this->fractal->item($updated)
            ->transformWith($this->getTransformer(ServerTransformer::class))
            ->toArray();
    }

    /**
     * Update the build details for a specific server.
     *
     * @throws \Hyperodactyl\Exceptions\DisplayException
     * @throws \Hyperodactyl\Exceptions\Model\DataValidationException
     * @throws \Hyperodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function build(UpdateServerBuildConfigurationRequest $request, Server $server): array
    {
        $server = $this->buildModificationService->handle($server, $request->validated());

        return $this->fractal->item($server)
            ->transformWith($this->getTransformer(ServerTransformer::class))
            ->toArray();
    }
}
