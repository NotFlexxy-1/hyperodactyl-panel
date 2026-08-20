<?php

namespace Hyperodactyl\Http\Controllers\Admin\Lxc;

use Illuminate\Http\Request;
use Hyperodactyl\Models\User;
use Hyperodactyl\Models\LxcNode;
use Hyperodactyl\Models\LxcContainer;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Hyperodactyl\Http\Controllers\Controller;
use Hyperodactyl\Services\Lxc\PowerActionService;
use Hyperodactyl\Services\Lxc\ResourceUpdateService;
use Hyperodactyl\Services\Lxc\ContainerCreationService;
use Hyperodactyl\Services\Lxc\ContainerDeletionService;
use Hyperodactyl\Services\Lxc\Drivers\LxcDriverFactory;
use Hyperodactyl\Exceptions\Lxc\LxcApiException;

class LxcContainerController extends Controller
{
    public function __construct(
        private PowerActionService $powerActionService,
        private ResourceUpdateService $resourceUpdateService,
        private ContainerCreationService $creationService,
        private ContainerDeletionService $deletionService,
        private LxcDriverFactory $driverFactory,
    ) {
    }

    public function index(Request $request): View
    {
        $containers = LxcContainer::query()
            ->with(['node', 'owner'])
            ->when($request->query('filter.name'), fn ($q, $name) => $q->where('name', 'like', "%{$name}%"))
            ->paginate(25);

        return view('admin.lxc.containers.index', ['containers' => $containers]);
    }

    public function create(): View
    {
        return view('admin.lxc.containers.new', [
            'nodes' => LxcNode::query()->where('maintenance_mode', false)->get(),
            'users' => User::all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(LxcContainer::$validationRules);
        $container = $this->creationService->handle($data);

        return redirect()->route('admin.lxc.containers.view', $container->id);
    }

    public function view(LxcContainer $container): View
    {
        $usage = null;
        $error = null;

        try {
            $usage = $this->driverFactory->forNode($container->node)->resources($container);
        } catch (LxcApiException $exception) {
            $error = $exception->getMessage();
        }

        return view('admin.lxc.containers.view', [
            'container' => $container,
            'usage' => $usage,
            'error' => $error,
            'nodes' => LxcNode::all(),
            'users' => User::all(),
        ]);
    }

    /**
     * Updates container metadata, resource limits, and/or reassigns the owner. Any
     * resource limit change is pushed to the remote node via the real driver API.
     */
    public function update(Request $request, LxcContainer $container): RedirectResponse
    {
        $rules = LxcContainer::$validationRules;
        $rules['name'] = 'sometimes|string|max:100';
        $rules['owner_id'] = 'sometimes|exists:users,id';
        $rules['lxc_node_id'] = 'sometimes|exists:lxc_nodes,id';
        $rules['image'] = 'sometimes|string';
        $rules['memory'] = 'sometimes|integer|min:16';
        $rules['disk'] = 'sometimes|integer|min:128';

        $data = $request->validate($rules);

        $limitFields = array_intersect_key($data, array_flip(['memory', 'swap', 'disk', 'cpu_limit', 'io_weight']));
        $metaFields = array_diff_key($data, $limitFields);

        if (!empty($metaFields)) {
            $container->fill($metaFields);
            $container->save();
        }

        if (!empty($limitFields)) {
            $this->resourceUpdateService->handle($container, $limitFields);
        }

        return redirect()->route('admin.lxc.containers.view', $container->id);
    }

    /**
     * Reassigns the owner of a container without touching any other data.
     */
    public function reassign(Request $request, LxcContainer $container): RedirectResponse
    {
        $data = $request->validate(['owner_id' => 'required|exists:users,id']);
        $container->update($data);

        return redirect()->route('admin.lxc.containers.view', $container->id);
    }

    /**
     * Performs a real power action (start/stop/restart/freeze) against the container.
     */
    public function power(Request $request, LxcContainer $container): RedirectResponse
    {
        $data = $request->validate(['action' => 'required|in:start,stop,restart,freeze']);

        $this->powerActionService->handle($container, $data['action']);

        return redirect()->route('admin.lxc.containers.view', $container->id);
    }

    /**
     * Deletes a container. When force=1 the local database record is removed even
     * if the remote node call fails.
     */
    public function delete(Request $request, LxcContainer $container): RedirectResponse
    {
        $this->deletionService->handle($container, $request->boolean('force'));

        return redirect()->route('admin.lxc.containers');
    }
}
