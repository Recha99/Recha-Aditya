<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ToolTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_tool()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create(['nama_kategori' => 'Test Category']);

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
}
