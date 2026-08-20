<?php

namespace Hyperodactyl\Http\Controllers\Api\Application\Lxc;

use Hyperodactyl\Models\LxcContainer;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\QueryBuilder;
use Hyperodactyl\Services\Lxc\ContainerCreationService;
use Hyperodactyl\Services\Lxc\ContainerDeletionService;
use Hyperodactyl\Services\Lxc\ResourceUpdateService;
use Hyperodactyl\Transformers\Api\Application\LxcContainerTransformer;
use Hyperodactyl\Http\Controllers\Api\Application\ApplicationApiController;
use Hyperodactyl\Http\Requests\Api\Application\Lxc\GetLxcContainersRequest;
use Hyperodactyl\Http\Requests\Api\Application\Lxc\StoreLxcContainerRequest;
use Hyperodactyl\Http\Requests\Api\Application\Lxc\UpdateLxcContainerRequest;
use Hyperodactyl\Http\Requests\Api\Application\Lxc\DeleteLxcContainerRequest;

class LxcContainerController extends ApplicationApiController
{
    public function __construct(
        private ContainerCreationService $creationService,
        private ContainerDeletionService $deletionService,
        private ResourceUpdateService $resourceUpdateService,
    ) {
        parent::__construct();
    }

    public function index(GetLxcContainersRequest $request): array
    {
        $containers = QueryBuilder::for(LxcContainer::query()->with(['node', 'owner']))
            ->allowedFilters(['uuid', 'name', 'owner_id', 'lxc_node_id', 'status'])
            ->allowedSorts(['id', 'name', 'created_at'])
            ->paginate($request->query('per_page') ?? 50);

        return $this->fractal->collection($containers)
            ->transformWith($this->getTransformer(LxcContainerTransformer::class))
            ->toArray();
    }

    public function view(GetLxcContainersRequest $request, LxcContainer $container): array
    {
        return $this->fractal->item($container)
            ->transformWith($this->getTransformer(LxcContainerTransformer::class))
            ->toArray();
    }

    public function store(StoreLxcContainerRequest $request): JsonResponse
    {
        $container = $this->creationService->handle($request->validated());

        return $this->fractal->item($container)
            ->transformWith($this->getTransformer(LxcContainerTransformer::class))
            ->addMeta([
                'resource' => route('api.application.lxc-containers.view', ['container' => $container->id]),
            ])
            ->respond(201);
    }

    /**
     * Updates metadata and, if resource limits are present in the payload, pushes
     * those updated limits to the remote node via the real driver API.
     */
    public function update(UpdateLxcContainerRequest $request, LxcContainer $container): array
    {
        $data = $request->validated();

        $limitFields = array_intersect_key($data, array_flip(['memory', 'swap', 'disk', 'cpu_limit', 'io_weight']));
        $metaFields = array_diff_key($data, $limitFields);

        if (!empty($metaFields)) {
            $container->fill($metaFields);
            $container->save();
        }

        if (!empty($limitFields)) {
            $container = $this->resourceUpdateService->handle($container, $limitFields);
        }

        return $this->fractal->item($container->fresh())
            ->transformWith($this->getTransformer(LxcContainerTransformer::class))
            ->toArray();
    }

    /**
     * Deletes a container. If ?force=true is passed the database record is removed
     * even if the remote node call fails (e.g. node offline).
     */
    public function delete(DeleteLxcContainerRequest $request, LxcContainer $container): JsonResponse
    {
        $this->deletionService->handle($container, $request->boolean('force'));

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
    }
}
