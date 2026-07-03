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
        Schema::create('tv_lists', function (Blueprint $table) {
            $table->uuid('id')->unique()->autoIncrement();
            $table->string('acara');
            $table->string('thumb')->nullable();
            $table->enum('category', ['unsorted'])->default('unsorted')->nullable();
            $table->bigInteger('rate')->nullable();
            $table->string('url');
            $table->enum('status', ['online', 'offline'])->default('online');
            $table->bigInteger('watched')->nullable()->default(0);
            $table->string('country')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tv_lists');
    }
};
