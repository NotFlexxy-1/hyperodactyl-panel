<?php

namespace Hyperodactyl\Http\ViewComposers;

use Illuminate\View\View;
use Hyperodactyl\Services\Helpers\AssetHashService;
use Hyperodactyl\Services\Hyperodactyl\BrandingService;

class AssetComposer
{
    /**
     * AssetComposer constructor.
     */
    public function __construct(private AssetHashService $assetHashService, private BrandingService $branding)
    {
    }

    /**
     * Provide access to the asset service in the views.
     */
    public function compose(View $view): void
    {
        $branding = $this->branding->all();

        $view->with('asset', $this->assetHashService);
        $view->with('branding', $branding);
        $view->with('brandingCssVariables', $this->branding->cssVariables());
        $view->with('siteConfiguration', [
            'name' => $branding['site_name'] ?? (config('app.name') ?? 'Hyperodactyl'),
            'locale' => config('app.locale') ?? 'en',
            'recaptcha' => [
                'enabled' => config('recaptcha.enabled', false),
                'siteKey' => config('recaptcha.website_key') ?? '',
            ],
            'branding' => $branding,
        ]);
    }
}
