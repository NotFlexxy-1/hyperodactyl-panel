<?php

namespace Hyperodactyl\Http\Controllers\Api\Client;

use Hyperodactyl\Services\Hyperodactyl\BrandingService;

/**
 * Exposes the public/safe branding & theme settings to the SPA so it can
 * re-apply CSS variables at runtime (e.g. after an admin makes a change).
 */
class HyperBrandingController extends ClientApiController
{
    public function __construct(private BrandingService $branding)
    {
        parent::__construct();
    }

    public function index(): array
    {
        return ['object' => 'branding', 'attributes' => $this->branding->publicBranding()];
    }
}
