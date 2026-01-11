<?php

namespace App\Http\Controllers;

use App\Models\QuestionBank;
use App\Models\QuestionBankCategory;
use App\Models\QuestionBankOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionBankController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user();

        $query = QuestionBank::where('teacher_id', $teacher->id)
            ->with(['category', 'options']);

        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('question_text', 'like', '%' . $request->search . '%');
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('question_type', $request->type);
        }

        $questions = $query->latest()->paginate(15);
        $categories = QuestionBankCategory::where('teacher_id', $teacher->id)->get();

        return view('guru.question-bank.index', compact('questions', 'categories'));
    }

    public function create()
    {
        $categories = QuestionBankCategory::where('teacher_id', Auth::id())->get();
        return view('guru.question-bank.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:question_bank_categories,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,essay',
            'points' => 'required|numeric|min:0',
            'explanation' => 'nullable|string',
            'options' => 'nullable|array',
            'options.*.option_text' => 'nullable|string',
            'correct_option' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            $question = QuestionBank::create([
                'category_id' => $validated['category_id'] ?? null,
                'teacher_id' => Auth::id(),
                'question_text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
                'points' => $validated['points'],
                'explanation' => $validated['explanation'] ?? null,
            ]);

            if ($validated['question_type'] === 'multiple_choice' && isset($validated['options'])) {
                foreach ($validated['options'] as $index => $option) {
                    $question->options()->create([
                        'option_text' => $option['option_text'],
                        'is_correct' => isset($validated['correct_option']) && $validated['correct_option'] == $index,
                        'order' => $index + 1,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('teacher.question-bank.index')->with('success', 'Soal berhasil ditambahkan ke bank soal.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan soal: ' . $e->getMessage());
        }
    }

    public function edit(QuestionBank $questionBank)
    {
        if ($questionBank->teacher_id !== Auth::id()) {
            abort(403);
        }

        $categories = QuestionBankCategory::where('teacher_id', Auth::id())->get();
        $questionBank->load('options');

        return view('guru.question-bank.edit', compact('questionBank', 'categories'));
    }

    public function update(Request $request, QuestionBank $questionBank)
    {
        if ($questionBank->teacher_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'category_id' => 'nullable|exists:question_bank_categories,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,essay',
            'points' => 'required|numeric|min:0',
            'explanation' => 'nullable|string',
            'options' => 'nullable|array',
            'options.*.option_text' => 'nullable|string',
            'correct_option' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            $questionBank->update([
                'category_id' => $validated['category_id'] ?? null,
                'question_text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
                'points' => $validated['points'],
                'explanation' => $validated['explanation'] ?? null,
            ]);

            // Delete old options and recreate
            $questionBank->options()->delete();

            if ($validated['question_type'] === 'multiple_choice' && isset($validated['options'])) {
                foreach ($validated['options'] as $index => $option) {
                    $questionBank->options()->create([
                        'option_text' => $option['option_text'],
                        'is_correct' => isset($validated['correct_option']) && $validated['correct_option'] == $index,
                        'order' => $index + 1,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('teacher.question-bank.index')->with('success', 'Soal berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal update soal: ' . $e->getMessage());
        }
    }

    public function destroy(QuestionBank $questionBank)
    {
        if ($questionBank->teacher_id !== Auth::id()) {
            abort(403);
        }

        $questionBank->delete();
        return redirect()->route('teacher.question-bank.index')->with('success', 'Soal berhasil dihapus dari bank soal.');
    }

    // Category Management
    public function categories()
    {
        $categories = QuestionBankCategory::where('teacher_id', Auth::id())->withCount('questions')->get();
        return view('guru.question-bank.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        QuestionBankCategory::create([
            'teacher_id' => Auth::id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    // API endpoint for quiz import
    public function getQuestionsForImport(Request $request)
    {
        $teacher = Auth::user();

        $query = QuestionBank::where('teacher_id', $teacher->id)
            ->with(['category', 'options']);

        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('question_text', 'like', '%' . $request->search . '%');
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('question_type', $request->type);
        }

        $questions = $query->latest()->get();

        return response()->json([
            'questions' => $questions,
            'categories' => QuestionBankCategory::where('teacher_id', $teacher->id)->get(),
        ]);
    }
}
