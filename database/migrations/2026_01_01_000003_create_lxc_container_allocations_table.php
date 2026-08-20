<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lxc_container_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lxc_container_id');
            $table->string('protocol')->default('tcp');
            $table->unsignedInteger('host_port');
            $table->unsignedInteger('container_port');
            $table->timestamps();

            $table->foreign('lxc_container_id')->references('id')->on('lxc_containers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lxc_container_allocations');
    }
};
