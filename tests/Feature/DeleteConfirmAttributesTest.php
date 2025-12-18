<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Course;

class DeleteConfirmAttributesTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_show_contains_delete_form_with_data_confirm()
    {
        $teacher = User::factory()->create(['role_id' => 2]);
        $course = Course::create([
            'name' => 'Test Kelas',
            'description' => 'Desc',
            'code' => 'ABCDEFGH',
            'teacher_id' => $teacher->id,
        ]);

        $this->actingAs($teacher);

        $response = $this->get(route('teacher.courses.show', $course));
        $response->assertStatus(200);

        // assert presence of data-confirm attribute on the delete form
        $response->assertSee('data-confirm="Yakin ingin menghapus kelas ini?"', false);
    }
}
