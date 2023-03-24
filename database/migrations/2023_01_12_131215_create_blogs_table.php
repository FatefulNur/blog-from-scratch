<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->uuid('slug');
            $table->text('short_desc')->nullable();
            $table->longText('description');
            $table->foreignId('user_id')->constrained();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->boolean("featured")->nullable();
            $table->boolean('can_commented');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blogs');
    }
};
