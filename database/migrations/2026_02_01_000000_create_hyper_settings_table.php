<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hyper_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        // Seed sensible defaults so the panel renders correctly before an
        // administrator ever visits the branding/customization screen.
        $now = now();
        $defaults = [
            'site_name' => 'Hyperodactyl',
            'site_short_name' => 'HD',
            'logo_url' => '',
            'favicon_url' => '/favicons/favicon.ico',
            'social_description' => 'A modern game server management panel.',
            'meta_keywords' => 'game server, hosting, panel, hyperodactyl',
            'og_image_url' => '',
            'color_primary' => '#5b8cff',
            'color_accent' => '#5b8cff',
            'color_background' => '#0b0e14',
            'color_surface' => '#141821',
            'color_text' => '#f5f7fa',
            'color_danger' => '#e6485d',
            'color_success' => '#3bd671',
            'border_radius' => '0.75rem',
            'font' => 'Inter, sans-serif',
            'sidebar_layout' => json_encode([
                ['key' => 'overview', 'label' => 'Overview', 'icon' => 'home', 'enabled' => true, 'order' => 0],
                ['key' => 'servers', 'label' => 'Servers', 'icon' => 'server', 'enabled' => true, 'order' => 1],
                ['key' => 'account', 'label' => 'Account', 'icon' => 'user', 'enabled' => true, 'order' => 2],
            ]),
            'dashboard_widgets' => json_encode([
                ['key' => 'server-list', 'enabled' => true, 'order' => 0],
                ['key' => 'announcements', 'enabled' => true, 'order' => 1],
            ]),
            'allow_user_theme_override' => '0',
            'user_customizable_keys' => json_encode(['color_accent']),
        ];

        $rows = [];
        foreach ($defaults as $key => $value) {
            $rows[] = ['key' => $key, 'value' => $value, 'created_at' => $now, 'updated_at' => $now];
        }

        DB::table('hyper_settings')->insert($rows);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hyper_settings');
    }
};
