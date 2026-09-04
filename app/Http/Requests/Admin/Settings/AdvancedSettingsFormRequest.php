<?php

namespace Hyperodactyl\Http\Requests\Admin\Settings;

use Hyperodactyl\Http\Requests\Admin\AdminFormRequest;

class AdvancedSettingsFormRequest extends AdminFormRequest
{
    /**
     * Return all the rules to apply to this request's data.
     */
    public function rules(): array
    {
        return [
            'recaptcha:enabled' => 'required|in:true,false',
            'recaptcha:secret_key' => 'required|string|max:191',
            'recaptcha:website_key' => 'required|string|max:191',
            'hyperodactyl:guzzle:timeout' => 'required|integer|between:1,60',
            'hyperodactyl:guzzle:connect_timeout' => 'required|integer|between:1,60',
            'hyperodactyl:client_features:allocations:enabled' => 'required|in:true,false',
            'hyperodactyl:client_features:allocations:range_start' => [
                'nullable',
                'required_if:hyperodactyl:client_features:allocations:enabled,true',
                'integer',
                'between:1024,65535',
            ],
            'hyperodactyl:client_features:allocations:range_end' => [
                'nullable',
                'required_if:hyperodactyl:client_features:allocations:enabled,true',
                'integer',
                'between:1024,65535',
                'gt:hyperodactyl:client_features:allocations:range_start',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'recaptcha:enabled' => 'reCAPTCHA Enabled',
            'recaptcha:secret_key' => 'reCAPTCHA Secret Key',
            'recaptcha:website_key' => 'reCAPTCHA Website Key',
            'hyperodactyl:guzzle:timeout' => 'HTTP Request Timeout',
            'hyperodactyl:guzzle:connect_timeout' => 'HTTP Connection Timeout',
            'hyperodactyl:client_features:allocations:enabled' => 'Auto Create Allocations Enabled',
            'hyperodactyl:client_features:allocations:range_start' => 'Starting Port',
            'hyperodactyl:client_features:allocations:range_end' => 'Ending Port',
        ];
    }
}
