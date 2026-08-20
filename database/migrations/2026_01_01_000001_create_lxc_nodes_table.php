<?php

use Illuminate\Support\Str;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lxc_nodes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('fqdn');
            $table->string('scheme')->default('https');
            $table->unsignedSmallInteger('port')->default(8443);
            $table->enum('driver', ['lxd', 'proxmox'])->default('lxd');
            $table->text('api_token');
            $table->text('api_secret')->nullable();
            $table->boolean('tls_verify')->default(true);
            $table->string('proxmox_node')->nullable();
            $table->string('storage_pool')->default('default');
            $table->string('network_bridge')->default('lxdbr0');
            $table->boolean('maintenance_mode')->default(false);
            $table->unsignedBigInteger('memory');
            $table->unsignedInteger('memory_overallocate')->default(0);
            $table->unsignedBigInteger('disk');
            $table->unsignedInteger('disk_overallocate')->default(0);
            $table->unsignedInteger('cpu')->default(0);
            $table->unsignedInteger('cpu_overallocate')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lxc_nodes');
    }
};
