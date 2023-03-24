<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Setting>
 */
class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'site_name' => $this->faker->word() . "Blog",
            'tagline' => $this->faker->paragraph(1),
            'membership' => 0,
            'default_role' => 2,
            'max_depth_comment' => 2,
            'comment_permission' => 0,
        ];
    }
}
