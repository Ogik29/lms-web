<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\QuestionBankCategory;
use App\Models\QuestionBank;
use App\Models\QuestionBankOption;

class QuestionBankSeeder extends Seeder
{
    public function run(): void
    {
        // Get first teacher
        $teacher = User::where('role_id', 2)->first();

        if (!$teacher) {
            $this->command->warn('No teacher found. Skipping question bank seeder.');
            return;
        }

        // Create categories
        $categories = [
            [
                'name' => 'Laravel',
                'description' => 'Soal-soal tentang Laravel Framework',
            ],
            [
                'name' => 'PHP',
                'description' => 'Soal-soal tentang bahasa pemrograman PHP',
            ],
            [
                'name' => 'Database',
                'description' => 'Soal-soal tentang database dan SQL',
            ],
        ];

        $createdCategories = [];
        foreach ($categories as $catData) {
            $createdCategories[] = QuestionBankCategory::create([
                'teacher_id' => $teacher->id,
                'name' => $catData['name'],
                'description' => $catData['description'],
            ]);
        }

        // Sample questions
        $questions = [
            // Laravel Questions
            [
                'category_id' => $createdCategories[0]->id,
                'question_text' => 'Apa yang dimaksud dengan MVC dalam Laravel?',
                'question_type' => 'multiple_choice',
                'points' => 10,
                'explanation' => 'MVC adalah pola arsitektur yang memisahkan aplikasi menjadi Model (data), View (tampilan), dan Controller (logika).',
                'options' => [
                    ['text' => 'Model-View-Controller, pola arsitektur untuk mengorganisir code', 'correct' => true],
                    ['text' => 'Main-Variable-Class, struktur class utama', 'correct' => false],
                    ['text' => 'Multiple-Version-Control, sistem version control', 'correct' => false],
                    ['text' => 'Managed-Virtual-Container, container management', 'correct' => false],
                ],
            ],
            [
                'category_id' => $createdCategories[0]->id,
                'question_text' => 'Jelaskan perbedaan antara Route::get() dan Route::post() di Laravel!',
                'question_type' => 'essay',
                'points' => 15,
                'explanation' => 'Route::get() untuk HTTP GET request (mengambil data), Route::post() untuk HTTP POST request (mengirim data).',
                'options' => [],
            ],
            // PHP Questions
            [
                'category_id' => $createdCategories[1]->id,
                'question_text' => 'Apa fungsi dari operator ?? (null coalescing) di PHP?',
                'question_type' => 'multiple_choice',
                'points' => 10,
                'explanation' => 'Operator ?? mengembalikan operand pertama jika bukan null, jika null maka mengembalikan operand kedua.',
                'options' => [
                    ['text' => 'Mengembalikan nilai default jika variable null', 'correct' => true],
                    ['text' => 'Mengecek apakah dua nilai sama', 'correct' => false],
                    ['text' => 'Melakukan operasi matematika', 'correct' => false],
                    ['text' => 'Menggabungkan dua string', 'correct' => false],
                ],
            ],
            // Database Questions
            [
                'category_id' => $createdCategories[2]->id,
                'question_text' => 'Apa perbedaan INNER JOIN dan LEFT JOIN?',
                'question_type' => 'multiple_choice',
                'points' => 12,
                'explanation' => 'INNER JOIN hanya mengembalikan rows yang match di kedua tabel. LEFT JOIN mengembalikan semua rows dari tabel kiri dan rows yang match dari tabel kanan.',
                'options' => [
                    ['text' => 'INNER JOIN hanya rows yang match, LEFT JOIN semua dari kiri + match dari kanan', 'correct' => true],
                    ['text' => 'INNER JOIN lebih cepat dari LEFT JOIN', 'correct' => false],
                    ['text' => 'LEFT JOIN tidak bisa digunakan dengan WHERE clause', 'correct' => false],
                    ['text' => 'Tidak ada perbedaan, keduanya sama', 'correct' => false],
                ],
            ],
            [
                'category_id' => $createdCategories[2]->id,
                'question_text' => 'Jelaskan apa itu Database Normalization dan mengapa penting!',
                'question_type' => 'essay',
                'points' => 20,
                'explanation' => 'Normalization adalah proses mengorganisir data untuk mengurangi redundansi dan dependency.',
                'options' => [],
            ],
        ];

        foreach ($questions as $qData) {
            $question = QuestionBank::create([
                'category_id' => $qData['category_id'],
                'teacher_id' => $teacher->id,
                'question_text' => $qData['question_text'],
                'question_type' => $qData['question_type'],
                'points' => $qData['points'],
                'explanation' => $qData['explanation'],
            ]);

            if ($qData['question_type'] === 'multiple_choice' && !empty($qData['options'])) {
                foreach ($qData['options'] as $index => $option) {
                    QuestionBankOption::create([
                        'question_bank_id' => $question->id,
                        'option_text' => $option['text'],
                        'is_correct' => $option['correct'],
                        'order' => $index + 1,
                    ]);
                }
            }
        }

        $this->command->info('Question bank seeded successfully with ' . count($questions) . ' questions!');
    }
}
