<?php

namespace Hyperodactyl\Http\Controllers\Admin\Hyper;

use Illuminate\View\View;
use Hyperodactyl\Models\User;
use Illuminate\Http\Request;
use Hyperodactyl\Models\HyperAchievement;
use Hyperodactyl\Models\HyperStoreItem;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Hyperodactyl\Http\Controllers\Controller;
use Hyperodactyl\Exceptions\DisplayException;
use Hyperodactyl\Services\Hyperodactyl\Economy\CoinService;

class HyperEconomyController extends Controller
{
    public function __construct(
        protected AlertsMessageBag $alert,
        protected CoinService $coinService,
    ) {
    }

    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->query('filter'), fn ($query, $filter) => $query->where('username', 'like', "%{$filter}%")->orWhere('email', 'like', "%{$filter}%"))
            ->orderBy('id')
            ->paginate(25);

        return view('admin.hyper.index', ['users' => $users]);
    }

    public function adjustBalance(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'amount' => 'required|integer|not_in:0',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $this->coinService->adjust($user, (int) $data['amount'], $data['description'] ?? 'Admin balance adjustment');
            $this->alert->success('The user\'s Hyper Coin balance has been updated.')->flash();
        } catch (DisplayException $exception) {
            $this->alert->danger($exception->getMessage())->flash();
        }

        return redirect()->route('admin.hyper.index');
    }

    public function store(): View
    {
        return view('admin.hyper.store', ['items' => HyperStoreItem::query()->orderBy('id')->get()]);
    }

    public function storeCreate(Request $request): RedirectResponse
    {
        $data = $request->validate(HyperStoreItem::$validationRules);
        $data['enabled'] = $request->boolean('enabled');
        $data['effect'] = json_decode($request->input('effect', '{}'), true) ?? [];

        HyperStoreItem::query()->create($data);
        $this->alert->success('Store item created.')->flash();

        return redirect()->route('admin.hyper.store');
    }

    public function storeUpdate(Request $request, HyperStoreItem $item): RedirectResponse
    {
        if ($request->input('action') === 'delete') {
            $item->delete();
            $this->alert->success('Store item deleted.')->flash();

            return redirect()->route('admin.hyper.store');
        }

        $data = $request->validate(HyperStoreItem::$validationRules);
        $data['enabled'] = $request->boolean('enabled');
        $data['effect'] = json_decode($request->input('effect', '{}'), true) ?? [];

        $item->update($data);
        $this->alert->success('Store item updated.')->flash();

        return redirect()->route('admin.hyper.store');
    }

    public function achievements(): View
    {
        return view('admin.hyper.achievements', ['achievements' => HyperAchievement::query()->orderBy('id')->get()]);
    }

    public function achievementCreate(Request $request): RedirectResponse
    {
        $data = $request->validate(HyperAchievement::$validationRules);
        $data['criteria'] = json_decode($request->input('criteria', '{}'), true) ?? [];

        HyperAchievement::query()->create($data);
        $this->alert->success('Achievement created.')->flash();

        return redirect()->route('admin.hyper.achievements');
    }

    public function achievementUpdate(Request $request, HyperAchievement $achievement): RedirectResponse
    {
        if ($request->input('action') === 'delete') {
            $achievement->delete();
            $this->alert->success('Achievement deleted.')->flash();

            return redirect()->route('admin.hyper.achievements');
        }

        $rules = HyperAchievement::$validationRules;
        $rules['key'] = 'required|string|max:191|unique:hyper_achievements,key,' . $achievement->id;

        $data = $request->validate($rules);
        $data['criteria'] = json_decode($request->input('criteria', '{}'), true) ?? [];

        $achievement->update($data);
        $this->alert->success('Achievement updated.')->flash();

        return redirect()->route('admin.hyper.achievements');
    }
}
