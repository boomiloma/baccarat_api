<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('config', function (Blueprint $table) {
            $table->id();
            $table->string('desk_name')->nullable();
            $table->integer('boot_num')->nullable();
            $table->integer('double_max')->nullable();
            $table->integer('double_small')->nullable();
            $table->integer('draw_small')->nullable();
            $table->integer('draw_max')->nullable();
            $table->integer('six_max')->nullable();
            $table->integer('six_small')->nullable();
            $table->integer('banker_and_player_max')->nullable();
            $table->integer('banker_and_player_small')->nullable();
            $table->integer('banker_and_player_max_th')->nullable();
            $table->integer('banker_and_player_small_th')->nullable();
            $table->integer('double_max_th')->nullable();
            $table->integer('double_small_th')->nullable();
            $table->integer('draw_max_th')->nullable();
            $table->integer('draw_small_th')->nullable();

            $table->integer('game_num')->nullable();
            $table->integer('is_online')->nullable();
            $table->integer('second')->nullable();
            $table->integer('status')->nullable();
            $table->string('verify')->nullable();
            $table->string('create_by')->nullable();
            $table->string('update_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config');

    }
};
