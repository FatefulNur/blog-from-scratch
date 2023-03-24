<?php

namespace Database\Factories;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'caption' => $this->faker->text(12),
            'details' => $this->faker->realText(),
            'path' => $this->faker->imageUrl(),
            'imagable_id' => 2,
            'imagable_type' => '\App\Models\Blog'
        ];
    }
}
