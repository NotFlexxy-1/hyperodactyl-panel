<?php

namespace Hyperodactyl\Http\Controllers\Api\Application;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Hyperodactyl\Services\Hyperodactyl\BrandingService;

class BrandingController extends ApplicationApiController
{
    public function __construct(private BrandingService $branding)
    {
        parent::__construct();
    }

    public function index(): array
    {
        return ['object' => 'branding', 'attributes' => $this->branding->all()];
    }

    public function update(Request $request): array
    {
        $validated = $request->validate([
            'site_name' => 'sometimes|required|string|max:191',
            'site_short_name' => 'sometimes|nullable|string|max:32',
            'logo_url' => 'sometimes|nullable|string|max:1024',
            'favicon_url' => 'sometimes|nullable|string|max:1024',
            'social_description' => 'sometimes|nullable|string|max:500',
            'meta_keywords' => 'sometimes|nullable|string|max:500',
            'og_image_url' => 'sometimes|nullable|string|max:1024',
            'color_primary' => 'sometimes|required|string|max:32',
            'color_accent' => 'sometimes|required|string|max:32',
            'color_background' => 'sometimes|required|string|max:32',
            'color_surface' => 'sometimes|required|string|max:32',
            'color_text' => 'sometimes|required|string|max:32',
            'color_danger' => 'sometimes|required|string|max:32',
            'color_success' => 'sometimes|required|string|max:32',
            'border_radius' => 'sometimes|nullable|string|max:32',
            'font' => 'sometimes|nullable|string|max:191',
            'sidebar_layout' => 'sometimes|array',
            'dashboard_widgets' => 'sometimes|array',
            'allow_user_theme_override' => 'sometimes|boolean',
            'user_customizable_keys' => 'sometimes|array',
        ]);

        return ['object' => 'branding', 'attributes' => $this->branding->update($validated)];
    }

    public function uploadLogo(Request $request): array
    {
        $request->validate(['file' => 'required|file|mimes:png,jpg,jpeg,svg,webp|max:2048']);

        $path = Storage::disk('public')->putFile('branding', $request->file('file'));
        $url = Storage::disk('public')->url($path);

        $this->branding->update(['logo_url' => $url]);

        return ['object' => 'branding_asset', 'attributes' => ['url' => $url]];
    }

    public function uploadFavicon(Request $request): array
    {
        $request->validate(['file' => 'required|file|mimes:png,ico,svg,webp|max:1024']);

        $path = Storage::disk('public')->putFile('branding', $request->file('file'));
        $url = Storage::disk('public')->url($path);

        $this->branding->update(['favicon_url' => $url]);

        return ['object' => 'branding_asset', 'attributes' => ['url' => $url]];
    }
}
