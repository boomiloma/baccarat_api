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
        Schema::create('game_result', function (Blueprint $table) {
            $table->id();
            $table->string('desk_name')->nullable();
            $table->integer('boot_num')->nullable();
            $table->integer('game_num')->nullable();
            $table->string('result')->nullable();
            $table->string('result_name')->nullable();
            $table->integer('status')->nullable();
            $table->integer('ask_turn_over')->nullable();
            $table->integer('is_interfered')->nullable();
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
        Schema::dropIfExists('game_result');
    }
};
