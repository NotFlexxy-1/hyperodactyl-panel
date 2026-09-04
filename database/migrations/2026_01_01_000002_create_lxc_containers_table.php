<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lxc_containers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('uuid_short')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('lxc_node_id');
            $table->string('image');
            $table->string('status')->nullable();
            $table->unsignedBigInteger('memory');
            $table->unsignedBigInteger('swap')->default(0);
            $table->unsignedBigInteger('disk');
            $table->unsignedInteger('cpu_limit')->default(0);
            $table->string('cpu_pinning')->nullable();
            $table->unsignedInteger('io_weight')->default(500);
            $table->string('ip_address')->nullable();
            $table->string('mac')->nullable();
            $table->text('ssh_key')->nullable();
            $table->text('root_password')->nullable();
            $table->timestamp('installed_at')->nullable();
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('lxc_node_id')->references('id')->on('lxc_nodes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lxc_containers');
    }
};
