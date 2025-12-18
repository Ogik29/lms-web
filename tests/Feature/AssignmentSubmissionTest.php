<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Course;
use App\Models\Assignment;

class AssignmentSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_submit_assignment_text()
    {
        $this->seed();

        $student = User::where('role_id', 3)->first();
        $assignment = Assignment::first();

        $this->actingAs($student)
            ->post(route('student.assignments.submit', $assignment), ['content' => 'Jawaban test'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('submissions', ['assignment_id' => $assignment->id, 'student_id' => $student->id]);
    }

    public function test_teacher_can_grade_submission()
    {
        $this->seed();

        $teacher = User::where('role_id', 2)->first();
        $student = User::where('role_id', 3)->first();
        $assignment = Assignment::first();

        // student submits
        $this->actingAs($student)->post(route('student.assignments.submit', $assignment), ['content' => 'Test']);

        $submission = $assignment->submissions()->where('student_id', $student->id)->first();

        $this->actingAs($teacher)->post(route('teacher.submissions.grade', $submission), ['score' => 90])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('submissions', ['id' => $submission->id, 'score' => 90]);
    }
}
