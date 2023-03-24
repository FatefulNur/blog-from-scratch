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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->tinyText('site_name')->nullable();
            $table->mediumText('tagline')->nullable();
            $table->boolean('membership')->nullable();
            $table->tinyInteger('default_role')->default(2);
            $table->boolean('allow_comment')->nullable();
            $table->boolean('nested_comment')->nullable();
            $table->tinyInteger('max_depth_comment')->default(3);
            $table->boolean('comment_permission')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
