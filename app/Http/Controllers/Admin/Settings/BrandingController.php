<?php

namespace Hyperodactyl\Http\Controllers\Admin\Settings;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Illuminate\Support\Facades\Storage;
use Hyperodactyl\Http\Controllers\Controller;
use Hyperodactyl\Services\Hyperodactyl\BrandingService;
use Hyperodactyl\Http\Requests\Admin\Settings\BrandingSettingsFormRequest;

class BrandingController extends Controller
{
    public function __construct(private BrandingService $branding, private AlertsMessageBag $alert)
    {
    }

    public function index(): View
    {
        return view('admin.settings.branding', [
            'branding' => $this->branding->all(),
        ]);
    }

    public function update(BrandingSettingsFormRequest $request): RedirectResponse
    {
        $data = $request->normalize();
        $data['allow_user_theme_override'] = $request->boolean('allow_user_theme_override');

        foreach (['sidebar_layout', 'dashboard_widgets', 'user_customizable_keys'] as $jsonKey) {
            if (empty($data[$jsonKey])) {
                unset($data[$jsonKey]);
            }
        }

        $this->branding->update($data);

        $this->alert->success('Branding and theme settings have been updated.')->flash();

        return redirect()->route('admin.settings.branding');
    }

    /**
     * Handle a logo upload from the branding form and store the resulting
     * public URL as the "logo_url" setting.
     */
    public function uploadLogo(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        $path = Storage::disk('public')->putFile('branding', $request->file('file'));
        $this->branding->update(['logo_url' => Storage::disk('public')->url($path)]);

        $this->alert->success('Logo uploaded successfully.')->flash();

        return redirect()->route('admin.settings.branding');
    }

    /**
     * Handle a favicon upload from the branding form and store the resulting
     * public URL as the "favicon_url" setting.
     */
    public function uploadFavicon(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:png,ico,svg,webp|max:1024',
        ]);

        $path = Storage::disk('public')->putFile('branding', $request->file('file'));
        $this->branding->update(['favicon_url' => Storage::disk('public')->url($path)]);

        $this->alert->success('Favicon uploaded successfully.')->flash();

        return redirect()->route('admin.settings.branding');
    }
}
