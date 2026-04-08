<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use DeepCopy\f013\C;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ToolTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_tool()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create(['nama_kategori' => 'Test category']);

        $response = $this->actingAs($admin)->post(route('tools.store'), [
            'nama_alat' => 'Test Tool',
            'category_id' => $category->id,
            'stok' => 10,
            'deskripsi' => 'Test description'
        ]);

        $response->assertRedirect(route('tools.index'));
        $this->assertDatabaseHas('tools', [
            'nama_alat' => 'Test Tool',
            'category_id' => $category->id,
            'stok' => 10
        ]);
    }

    public function test_admin_can_update_tool()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create(['nama_kategori' => 'Test category']);
        $tool = \App\Models\tools::factory()->create([
            'nama_alat' => 'Old Tool',
            'category_id' => $category->id,
            'stok' => 5
        ]);

        $response = $this->actingAs($admin)->put(route('tools.update', $tool), [
            'nama_alat' => 'Updated Tool',
            'category_id' => $category->id,
            'stok' => 10,
            'deskripsi' => 'Updated description'
        ]);

        $response->assertRedirect(route('tools.index'));
        $this->assertDatabaseHas('tools', [
            'nama_alat' => 'Updated Tool',
            'stok' => 10
        ]);
    }

    public function test_admin_can_delete_tool()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create(['nama_kategori' => 'Test category']);
        $tool = \App\Models\tools::factory()->create([
            'nama_alat' => 'Tool to Delete',
            'category_id' => $category->id,
            'stok' => 5
        ]);

        $response = $this->actingAs($admin)->delete(route('tools.destroy', $tool));

        $response->assertRedirect(route('tools.index'));
        $this->assertDatabaseMissing('tools', ['id' => $tool->id]);
    }
}
