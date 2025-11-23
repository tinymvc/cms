<?php

use Spark\Database\Schema\Blueprint;
use Spark\Database\Schema\Schema;

return new class {
    public function up(): void
    {
        Schema::create('posts_taxonomy', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->foreignId('taxonomy_id')->constrained('taxonomy')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts_taxonomy');
    }
};