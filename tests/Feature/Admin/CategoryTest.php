<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_see_create_category_page()
    {
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->get('/admin/categories/create')
            ->assertViewIs('admin.categories.create')
            ->assertViewHas('categories')
            ->assertOk();
    }

    /** @test */
    public function admin_can_create_a_category()
    {
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->post('/admin/categories/store', [
                'name' => 'Categoryg',
                'parent_id' => null,
                'depth' => 0
            ])
            ->assertValid()
            ->assertRedirect('/admin/categories/create')
            ->assertSessionHas('action');

        $this->assertCount(1, Category::all());
        $this->assertDatabaseCount('categories', 1);
    }

    /** @test */
    public function admin_can_create_an_icon_for_category()
    {
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->from('/admin/categories')
            ->post('/admin/categories/store', [
                'name' => 'Categoryg',
                'icon' => 'fab fa-facebook-f'
            ])
            ->assertRedirect('/admin/categories/create');

        $this->assertEquals('fab fa-facebook-f', Category::first()->icon);
    }

    /** @test */
    public function admin_can_delete_a_category()
    {
        $parent = Category::factory()->create();

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->delete('/admin/categories/' . $parent->name . '/delete')
            ->assertValid()
            ->assertRedirect('/admin/categories/create')
            ->assertSessionHas('action');

        $this->assertDatabaseEmpty('categories');
    }

    /** @test */
    public function admin_can_delete_child_category_if_exist()
    {
        $parent = Category::factory()->create();
        $child1 = Category::factory()->create([
            'name' => 'child 1',
            'parent_id' => $parent->id,
            'depth' => 1,
        ]);

        $child2 = Category::factory()->create([
            'name' => 'child 2',
            'parent_id' => $child1->id,
            'depth' => 2,
        ]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->delete('/admin/categories/' . $parent->name . '/delete')
            ->assertValid()
            ->assertRedirect('/admin/categories/create')
            ->assertSessionHas('action');

        $this->assertDatabaseEmpty('categories');
    }

    /** @test */
    public function admin_can_see_edit_category_page()
    {
        $parent = Category::factory()->create([
            "name" => "Good Category 1",
            "parent_id" => null
        ]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->get('/admin/categories/' . $parent->name . '/edit')
            ->assertViewIs('admin.categories.edit')
            ->assertViewHas('category')
            ->assertOk();
    }

    /** @test */
    public function admin_can_update_a_parent_category()
    {
        $parent = Category::factory()->create(['name' => 'hiategory']);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->put('/admin/categories/' . $parent->name . '/update', [
                'name' => 'Updated Cat 1',
                'parent_id' => null,
                'icon' => 'hi hi hi'
            ])
            ->assertValid()
            ->assertRedirect('/admin/categories/create')
            ->assertSessionHas('action');

        $this->assertDatabaseHas($parent, [
            'name' => 'Updated Cat 1',
            'parent_id' => null,
            'depth' => 0
        ]);
    }

    /** @test */
    public function admin_can_update_a_child_category_where_parent_set_to_null()
    {
        $parent = Category::factory()->create();
        $child1 = Category::factory()->create([
            'name' => 'child 1',
            'parent_id' => $parent->id,
            'depth' => 1,
        ]);
        $child2 = Category::factory()->create([
            'name' => 'child 2',
            'parent_id' => $child1->id,
            'depth' => 2,
        ]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->put('/admin/categories/' . $child1->name . '/update', [
                'name' => 'Updated Cat child',
                'parent_id' => null,
                'icon' => ""
            ])
            ->assertValid()
            ->assertRedirect('/admin/categories/create')
            ->assertSessionHas('action');

        $this->assertDatabaseHas($child1->fresh(), [
            'name' => 'Updated Cat child',
            'parent_id' => null,
            'icon' => "",
            'depth' => 0
        ]);

        $this->assertDatabaseHas($child2->fresh(), [
            'name' => 'child 2',
            'parent_id' => 2,
            'icon' => "",
            'depth' => 1
        ]);
    }

    /** @test */
    public function admin_can_update_a_child_category_where_parent_id_is_equalsTo_its_child_id()
    {
        $parent = Category::factory()->create();
        $child1 = Category::factory()->create([
            'name' => 'child 1',
            'parent_id' => $parent->id,
            'depth' => 1,
        ]);
        $child = Category::factory()->create([
            'name' => 'child',
            'parent_id' => $parent->id,
            'depth' => 1,
        ]);
        $child2 = Category::factory()->create([
            'name' => 'child 2',
            'parent_id' => $child1->id,
            'depth' => 2,
        ]);
        $child3 = Category::factory()->create([
            'name' => 'child 3',
            'parent_id' => $child2->id,
            'depth' => 3,
        ]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->put('/admin/categories/' . $child1->name . '/update', [
                'name' => 'Updated Cat child',
                'parent_id' => $child2->id,
                'icon' => ""
            ])
            ->assertValid()
            ->assertRedirect('/admin/categories/create')
            ->assertSessionHas('action');

        $this->assertDatabaseHas($child1->fresh(), [
            'name' => 'Updated Cat child',
            'parent_id' => 4,
            'icon' => "",
            'depth' => 2
        ]);

        $this->assertDatabaseHas($child2->fresh(), [
            'name' => 'child 2',
            'parent_id' => 1,
            'icon' => "",
            'depth' => 1
        ]);

        $this->assertDatabaseHas($child3->fresh(), [
            'name' => 'child 3',
            'parent_id' => 4,
            'icon' => "",
            'depth' => 2
        ]);
    }

    /** @test */
    public function admin_can_update_a_child_category_where_child_id_is_equalsTo_its_parent_id()
    {
        $parent = Category::factory()->create();
        $child1 = Category::factory()->create([
            'name' => 'child 1',
            'parent_id' => $parent->id,
            'depth' => 1,
        ]);
        $child2 = Category::factory()->create([
            'name' => 'child 2',
            'parent_id' => $child1->id,
            'depth' => 2,
        ]);
        $child3 = Category::factory()->create([
            'name' => 'child 3',
            'parent_id' => $child2->id,
            'depth' => 3,
        ]);
        $child4 = Category::factory()->create([
            'name' => 'child 4',
            'parent_id' => $child3->id,
            'depth' => 4,
        ]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->put('/admin/categories/' . $child3->name . '/update', [
                'name' => 'Updated Cat child',
                'parent_id' => $child1->id,
                'icon' => ""
            ])
            ->assertValid()
            ->assertRedirect('/admin/categories/create')
            ->assertSessionHas('action');

        $this->assertDatabaseHas($child3->fresh(), [
            'name' => 'Updated Cat child',
            'parent_id' => 2,
            'icon' => "",
            'depth' => 2
        ]);

        $this->assertDatabaseHas($child4->fresh(), [
            'name' => 'child 4',
            'parent_id' => 4,
            'icon' => "",
            'depth' => 3
        ]);
    }
}
