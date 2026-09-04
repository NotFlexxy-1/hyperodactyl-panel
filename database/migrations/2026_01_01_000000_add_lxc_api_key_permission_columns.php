<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLxcApiKeyPermissionColumns extends Migration
{
    public function up(): void
    {
        Schema::table('api_keys', function (Blueprint $table) {
            $table->unsignedTinyInteger('r_lxc_nodes')->default(0);
            $table->unsignedTinyInteger('r_lxc_containers')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('api_keys', function (Blueprint $table) {
            $table->dropColumn(['r_lxc_nodes', 'r_lxc_containers']);
        });
    }
}
