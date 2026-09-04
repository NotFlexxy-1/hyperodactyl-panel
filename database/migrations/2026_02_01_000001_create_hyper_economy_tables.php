<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHyperEconomyTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hyper_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->bigInteger('amount');
            $table->enum('type', ['earn', 'spend', 'admin_grant', 'refund', 'purchase', 'reward']);
            $table->string('description')->nullable();
            $table->json('meta')->nullable();
            $table->unsignedBigInteger('balance_after');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('hyper_store_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('category', ['resource', 'server_slot', 'cosmetic', 'other'])->default('other');
            $table->string('icon')->nullable();
            $table->unsignedBigInteger('price');
            $table->json('effect')->nullable();
            $table->boolean('enabled')->default(true);
            $table->unsignedInteger('stock')->nullable();
            $table->timestamps();
        });

        Schema::create('hyper_purchases', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('server_id')->nullable();
            $table->unsignedBigInteger('price_paid');
            $table->string('status')->default('completed');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('hyper_store_items')->onDelete('cascade');
            $table->foreign('server_id')->references('id')->on('servers')->onDelete('set null');
        });

        Schema::create('hyper_achievements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedBigInteger('coin_reward')->default(0);
            $table->json('criteria');
            $table->timestamps();
        });

        Schema::create('hyper_user_achievements', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('achievement_id');
            $table->timestamp('unlocked_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('achievement_id')->references('id')->on('hyper_achievements')->onDelete('cascade');
            $table->unique(['user_id', 'achievement_id']);
        });

        Schema::create('hyper_reward_claims', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->enum('kind', ['daily', 'hourly']);
            $table->timestamp('claimed_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'kind']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hyper_reward_claims');
        Schema::dropIfExists('hyper_user_achievements');
        Schema::dropIfExists('hyper_achievements');
        Schema::dropIfExists('hyper_purchases');
        Schema::dropIfExists('hyper_store_items');
        Schema::dropIfExists('hyper_transactions');
    }
}
