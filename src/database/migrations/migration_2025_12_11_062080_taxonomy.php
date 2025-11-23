<?php

use Spark\Database\Schema\Blueprint;
use Spark\Database\Schema\Schema;

return new class {
    public function up(): void
    {
        Schema::create('taxonomy', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('slug', 200)->unique();
            $table->string('image', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('type', 100)->nullable();
            $table->integer('parent_id')->nullable();
            $table->foreign('parent_id')->constrained('taxonomy')->cascadeOnDelete();
            $table->index(['name', 'type']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxonomy');
    }
};