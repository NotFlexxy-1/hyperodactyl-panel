<?php

namespace Hyperodactyl\Http\Controllers\Api\Client\Lxc;

use Illuminate\Http\Request;
use Hyperodactyl\Models\LxcContainer;
use Illuminate\Cache\Repository;
use Carbon\Carbon;
use Hyperodactyl\Http\Controllers\Api\Client\ClientApiController;
use Hyperodactyl\Transformers\Api\Client\LxcContainerTransformer;
use Hyperodactyl\Services\Lxc\PowerActionService;
use Hyperodactyl\Services\Lxc\ResourceUpdateService;
use Hyperodactyl\Services\Lxc\Drivers\LxcDriverFactory;

class LxcContainerController extends ClientApiController
{
    public function __construct(
        private PowerActionService $powerActionService,
        private ResourceUpdateService $resourceUpdateService,
        private LxcDriverFactory $driverFactory,
        private Repository $cache
    ) {
        parent::__construct();
    }

    public function index(Request $request): array
    {
        $containers = LxcContainer::query()
            ->when(!$request->user()->root_admin, fn ($q) => $q->where('owner_id', $request->user()->id))
            ->with(['node', 'allocations'])
            ->get();

        return $this->fractal->collection($containers)
            ->transformWith($this->getTransformer(LxcContainerTransformer::class))
            ->toArray();
    }

    public function view(Request $request, LxcContainer $container): array
    {
        $this->authorize('view', $container);

        return $this->fractal->item($container)
            ->transformWith($this->getTransformer(LxcContainerTransformer::class))
            ->toArray();
    }

    public function power(Request $request, LxcContainer $container)
    {
        $this->authorize('update', $container);

        $request->validate(['action' => 'required|in:start,stop,restart,freeze']);

        $this->powerActionService->handle($container, $request->input('action'));

        return $this->returnNoContent();
    }

    public function resources(Request $request, LxcContainer $container): array
    {
        $this->authorize('view', $container);

        $key = "lxc-resources:{$container->uuid}";

        return $this->cache->remember($key, Carbon::now()->addSeconds(10), function () use ($container) {
            return $this->driverFactory->forNode($container->node)->resources($container);
        });
    }

    public function update(Request $request, LxcContainer $container): array
    {
        $this->authorize('update', $container);

        $data = $request->validate([
            'memory' => 'sometimes|integer|min:16',
            'swap' => 'sometimes|integer|min:0',
            'disk' => 'sometimes|integer|min:128',
            'cpu_limit' => 'sometimes|integer|min:0',
            'io_weight' => 'sometimes|integer|between:10,1000',
        ]);

        $container = $this->resourceUpdateService->handle($container, $data);

        return $this->fractal->item($container)
            ->transformWith($this->getTransformer(LxcContainerTransformer::class))
            ->toArray();
    }

    public function console(Request $request, LxcContainer $container): array
    {
        $this->authorize('view', $container);

        return $this->driverFactory->forNode($container->node)->consoleUrl($container);
    }

    public function snapshots(Request $request, LxcContainer $container): array
    {
        $this->authorize('view', $container);

        return $this->driverFactory->forNode($container->node)->listSnapshots($container);
    }

    public function storeSnapshot(Request $request, LxcContainer $container): array
    {
        $this->authorize('update', $container);

        $data = $request->validate(['name' => 'required|string|max:60']);

        return $this->driverFactory->forNode($container->node)->createSnapshot($container, $data['name']);
    }

    public function restoreSnapshot(Request $request, LxcContainer $container, string $snapshot)
    {
        $this->authorize('update', $container);

        $this->driverFactory->forNode($container->node)->restoreSnapshot($container, $snapshot);

        return $this->returnNoContent();
    }
}
