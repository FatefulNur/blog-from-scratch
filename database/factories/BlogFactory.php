<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin\Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'slug' => $this->faker->uuid(),
            'short_desc' => $this->faker->realText(25),
            'description' => $this->faker->realText(),
            'user_id' => User::factory(),
            'deleted_at' => null,
            'can_commented' => $this->faker->boolean(),
            'featured' => $this->faker->boolean()
        ];
    }
}
