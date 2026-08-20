<?php

namespace Hyperodactyl\Http\Controllers\Api\Application\Lxc;

use Ramsey\Uuid\Uuid;
use Hyperodactyl\Models\LxcNode;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\QueryBuilder;
use Hyperodactyl\Transformers\Api\Application\LxcNodeTransformer;
use Hyperodactyl\Http\Controllers\Api\Application\ApplicationApiController;
use Hyperodactyl\Http\Requests\Api\Application\Lxc\GetLxcNodesRequest;
use Hyperodactyl\Http\Requests\Api\Application\Lxc\StoreLxcNodeRequest;
use Hyperodactyl\Http\Requests\Api\Application\Lxc\UpdateLxcNodeRequest;
use Hyperodactyl\Http\Requests\Api\Application\Lxc\DeleteLxcNodeRequest;

class LxcNodeController extends ApplicationApiController
{
    /**
     * Return a paginated listing of all LXC/Proxmox nodes registered on the panel.
     */
    public function index(GetLxcNodesRequest $request): array
    {
        $nodes = QueryBuilder::for(LxcNode::query()->withCount('containers'))
            ->allowedFilters(['uuid', 'name', 'fqdn', 'driver'])
            ->allowedSorts(['id', 'name', 'memory', 'disk'])
            ->paginate($request->query('per_page') ?? 50);

        return $this->fractal->collection($nodes)
            ->transformWith($this->getTransformer(LxcNodeTransformer::class))
            ->toArray();
    }

    public function view(GetLxcNodesRequest $request, LxcNode $node): array
    {
        return $this->fractal->item($node)
            ->transformWith($this->getTransformer(LxcNodeTransformer::class))
            ->toArray();
    }

    public function store(StoreLxcNodeRequest $request): JsonResponse
    {
        $node = LxcNode::create(array_merge($request->validated(), ['uuid' => Uuid::uuid4()->toString()]));

        return $this->fractal->item($node)
            ->transformWith($this->getTransformer(LxcNodeTransformer::class))
            ->addMeta([
                'resource' => route('api.application.lxc-nodes.view', ['node' => $node->id]),
            ])
            ->respond(201);
    }

    public function update(UpdateLxcNodeRequest $request, LxcNode $node): array
    {
        $data = $request->validated();
        if (empty($data['api_token'])) {
            unset($data['api_token']);
        }

        $node->update($data);

        return $this->fractal->item($node)
            ->transformWith($this->getTransformer(LxcNodeTransformer::class))
            ->toArray();
    }

    public function delete(DeleteLxcNodeRequest $request, LxcNode $node): JsonResponse
    {
        $node->delete();

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
    }
}
