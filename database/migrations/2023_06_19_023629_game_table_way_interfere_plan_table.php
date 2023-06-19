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
        Schema::create('table_way_interfere_plan', function (Blueprint $table) {
            $table->id();
            $table->string('desk_name')->nullable();
            $table->string('ip')->nullable();
            $table->string('machine_code')->nullable();
            $table->string('table_num')->nullable();
            $table->string('boot_num')->nullable();
            $table->string('not_expected')->nullable();
            $table->integer('status')->nullable();
            $table->integer('version')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_way_interfere_plan');

    }
};
