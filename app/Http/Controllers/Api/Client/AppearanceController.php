<?php

namespace Hyperodactyl\Http\Controllers\Api\Client;

use Illuminate\Http\Request;
use Hyperodactyl\Services\Hyperodactyl\BrandingService;

/**
 * Allows an authenticated user to read/persist their own theme overrides,
 * constrained server-side to whatever an administrator has permitted via
 * BrandingService (allow_user_theme_override + user_customizable_keys).
 */
class AppearanceController extends ClientApiController
{
    public function __construct(private BrandingService $branding)
    {
        parent::__construct();
    }

    public function index(Request $request): array
    {
        $settings = $this->branding->all();

        return [
            'object' => 'appearance',
            'attributes' => [
                'preferences' => $request->user()->theme_preferences ?? [],
                'allow_user_theme_override' => $settings['allow_user_theme_override'],
                'user_customizable_keys' => $settings['user_customizable_keys'],
            ],
        ];
    }

    public function update(Request $request): array
    {
        $data = $request->validate(['preferences' => 'required|array']);

        $filtered = $this->branding->filterUserAppearance($data['preferences']);

        $user = $request->user();
        $user->theme_preferences = $filtered;
        $user->save();

        return ['object' => 'appearance', 'attributes' => ['preferences' => $filtered]];
    }
}
