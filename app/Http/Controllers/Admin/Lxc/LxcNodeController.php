<?php

namespace Hyperodactyl\Http\Controllers\Admin\Lxc;

use Illuminate\Http\Request;
use Hyperodactyl\Models\LxcNode;
use Ramsey\Uuid\Uuid;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Hyperodactyl\Http\Controllers\Controller;
use Hyperodactyl\Services\Lxc\NodeHealthService;
use Hyperodactyl\Exceptions\Lxc\LxcApiException;

class LxcNodeController extends Controller
{
    public function __construct(private NodeHealthService $healthService)
    {
    }

    public function index(): View
    {
        return view('admin.lxc.nodes.index', ['nodes' => LxcNode::query()->withCount('containers')->paginate(25)]);
    }

    public function create(): View
    {
        return view('admin.lxc.nodes.new');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(LxcNode::$validationRules);
        $node = LxcNode::create(array_merge($data, ['uuid' => Uuid::uuid4()->toString()]));

        return redirect()->route('admin.lxc.nodes.view', $node->id);
    }

    public function view(LxcNode $node): View
    {
        $usage = null;
        $error = null;

        try {
            $usage = $this->healthService->handle($node);
        } catch (LxcApiException $exception) {
            $error = $exception->getMessage();
        }

        return view('admin.lxc.nodes.view', ['node' => $node, 'usage' => $usage, 'error' => $error]);
    }

    public function update(Request $request, LxcNode $node): RedirectResponse
    {
        $rules = LxcNode::$validationRules;
        $rules['api_token'] = 'nullable|string';

        $data = $request->validate($rules);
        if (empty($data['api_token'])) {
            unset($data['api_token']);
        }

        $node->update($data);

        return redirect()->route('admin.lxc.nodes.view', $node->id);
    }

    public function delete(LxcNode $node): RedirectResponse
    {
        $node->delete();

        return redirect()->route('admin.lxc.nodes');
    }
}
