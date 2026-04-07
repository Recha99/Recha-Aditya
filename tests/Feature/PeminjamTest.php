<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\tools;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PeminjamTest extends TestCase
{
    use RefreshDatabase;

    public function test_peminjam_can_borrow_tool()
    {
        $peminjam = User::factory()->create(['role' => 'peminjam']);
        $category = Category::factory()->create(['nama_kategori' => 'Test Category']);
        $tool = tools::create([
            'nama_alat' => 'Test Tool',
            'category_id' => $category->id,
            'stok' => 10,
            'deskripsi' => 'Test description'
        ]);

        $response = $this->actingAs($peminjam)->post('/peminjam/ajukan', [
            'tool_id' => $tool->id,
            'tanggal_kembali' => now()->addDays(7)->format('Y-m-d')
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('loans', [
            'user_id' => $peminjam->id,
            'tool_id' => $tool->id,
            'status' => 'pending'
        ]);
    }
}
