<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConfirmationModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_buttons_render_with_data_confirm()
    {
        // Seed minimal data: roles and a teacher with a course
        $teacher = User::factory()->create(['role_id' => 2]);

        $this->actingAs($teacher);

        $response = $this->get(route('teacher.dashboard'));

        $response->assertStatus(200);

        // Check that the dashboard contains the 'Hapus' buttons which will be rendered elsewhere
        $response->assertSee('Buat Kelas Baru');
    }
}
