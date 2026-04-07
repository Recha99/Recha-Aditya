<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_petugas_can_login()
    {
        $petugas = User::factory()->create(['role' => 'petugas']);

        $response = $this->post('/login', [
            'email' => $petugas->email,
            'password' => 'password'
        ]);

        $response->assertRedirect('/petugas/dashboard');
        $this->assertAuthenticatedAs($petugas);
    }

    public function test_peminjam_can_login()
    {
        $peminjam = User::factory()->create(['role' => 'peminjam']);

        $response = $this->post('/login', [
            'email' => $peminjam->email,
            'password' => 'password'
        ]);

        $response->assertRedirect('/peminjam/dashboard');
        $this->assertAuthenticatedAs($peminjam);
    }
}
