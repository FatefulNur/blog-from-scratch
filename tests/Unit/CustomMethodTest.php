<?php

namespace Tests\Unit;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomMethodTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function make_category_nested_to_route()
    {
        $category = Category::factory()->create(['name' => 'parent']);
        $children = Category::factory()->create(['name' => 'child', 'parent_id' => $category->id, 'depth' => 1]);
        $children2 = Category::factory()->create(['name' => 'child2', 'parent_id' => $children->id, 'depth' => 1]);
        $children3 = Category::factory()->create(['name' => 'child3', 'parent_id' => $children2->id, 'depth' => 2]);

        $response = $children3->ancestorsToRoute();


        $this->assertEquals("{$category->name}/{$children->name}/{$children2->name}/{$children3->name}", $response);
    }
}
