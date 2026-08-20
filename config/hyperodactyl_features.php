<?php

/*
|--------------------------------------------------------------------------
| Hyperodactyl Built-in Feature Flags
|--------------------------------------------------------------------------
|
| Hyperodactyl ships the most requested Pterodactyl addons and themes as
| first-class, built-in features. Everything below is enabled by default and
| can be toggled with environment variables — no addon installers, no patches.
|
*/

return [
    // Built-in themes (no external theme installer required).
    'theme' => [
        // arix | nebula | classic
        'default' => env('HYPERODACTYL_THEME', 'arix'),
        'allow_user_override' => env('HYPERODACTYL_THEME_USER_OVERRIDE', true),
        'accent' => env('HYPERODACTYL_THEME_ACCENT', '#5b8cff'),
        'logo' => env('HYPERODACTYL_LOGO', '/assets/hypernet-logo.png'),
        'brand_name' => env('HYPERODACTYL_BRAND', 'Hyperodactyl'),
    ],

    // Core upgrades over upstream Pterodactyl.
    'core' => [
        'server_splitter' => env('HYPERODACTYL_SERVER_SPLITTER', true),
        'nest_marketplace' => env('HYPERODACTYL_NEST_MARKETPLACE', true),
        'auto_backup_scheduler' => env('HYPERODACTYL_AUTO_BACKUPS', true),
        'per_server_subdomains' => env('HYPERODACTYL_SUBDOMAINS', true),
        'server_transfer_v2' => env('HYPERODACTYL_TRANSFER_V2', true),
        'audit_log_ui' => env('HYPERODACTYL_AUDIT_UI', true),
        'api_rate_limit_per_key' => env('HYPERODACTYL_API_RATE_LIMIT', 240),
    ],

    // Client-area features usually shipped as separate addons.
    'client' => [
        'resource_graphs' => env('HYPERODACTYL_GRAPHS', true),
        'file_editor_monaco' => env('HYPERODACTYL_MONACO', true),
        'server_notes' => env('HYPERODACTYL_SERVER_NOTES', true),
        'player_list' => env('HYPERODACTYL_PLAYER_LIST', true),
        'mod_installer' => env('HYPERODACTYL_MOD_INSTALLER', true),
        'plugin_installer' => env('HYPERODACTYL_PLUGIN_INSTALLER', true),
        'scheduled_restarts' => env('HYPERODACTYL_SCHEDULED_RESTARTS', true),
        'console_search' => env('HYPERODACTYL_CONSOLE_SEARCH', true),
    ],

    // Admin-area features.
    'admin' => [
        'dashboard_stats' => env('HYPERODACTYL_ADMIN_STATS', true),
        'node_health_monitor' => env('HYPERODACTYL_NODE_HEALTH', true),
        'bulk_actions' => env('HYPERODACTYL_BULK_ACTIONS', true),
        'maintenance_mode_banner' => env('HYPERODACTYL_MAINTENANCE_BANNER', true),
    ],

    // Security hardening enabled by default.
    'security' => [
        'force_two_factor_admins' => env('HYPERODACTYL_FORCE_2FA_ADMIN', true),
        'login_ip_alerts' => env('HYPERODACTYL_LOGIN_IP_ALERTS', true),
        'session_device_manager' => env('HYPERODACTYL_SESSION_DEVICES', true),
    ],
];
