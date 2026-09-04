<?php

namespace Hyperodactyl\Http\Requests\Admin\Settings;

use Hyperodactyl\Http\Requests\Admin\AdminFormRequest;

class BrandingSettingsFormRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'site_name' => 'required|string|max:191',
            'site_short_name' => 'nullable|string|max:32',
            'logo_url' => 'nullable|string|max:1024',
            'favicon_url' => 'nullable|string|max:1024',
            'social_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'og_image_url' => 'nullable|string|max:1024',
            'color_primary' => 'required|string|max:32',
            'color_accent' => 'required|string|max:32',
            'color_background' => 'required|string|max:32',
            'color_surface' => 'required|string|max:32',
            'color_text' => 'required|string|max:32',
            'color_danger' => 'required|string|max:32',
            'color_success' => 'required|string|max:32',
            'border_radius' => 'nullable|string|max:32',
            'font' => 'nullable|string|max:191',
            'sidebar_layout' => 'nullable|json',
            'dashboard_widgets' => 'nullable|json',
            'allow_user_theme_override' => 'nullable|in:0,1',
            'user_customizable_keys' => 'nullable|json',
        ];
    }
}
