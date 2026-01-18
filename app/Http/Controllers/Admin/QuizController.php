<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function show($id)
    {
        $lessonId = Crypt::decryptString($id);
        $lesson = Lesson::findOrFail($lessonId);
        
        // Get or create quiz for this lesson
        $quiz = Quiz::where('lesson_id', $lesson->id)->first();
        
        // Get all questions with answers
        $questions = $quiz 
            ? Question::where('quiz_id', $quiz->id)
                ->with('answers')
                ->orderBy('order')
                ->get()
            : collect();

        return view('admin.lessons.quiz', compact('lesson', 'quiz', 'questions'));
    }
    
    public function store(Request $request, $id)
    {
        $lessonId = Crypt::decryptString($id);
        $lesson = Lesson::findOrFail($lessonId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'passing_score' => 'required|integer|min:0|max:100',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['lesson_id'] = $lesson->id;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $quiz = Quiz::updateOrCreate(
            ['lesson_id' => $lesson->id],
            $validated
        );

        return redirect()
            ->route('admin.lessons.quiz.show', Crypt::encryptString($lesson->id))
            ->with('success', 'Quiz settings saved successfully!');
    }

    public function storeQuestion(Request $request, $id)
    {
        $lessonId = Crypt::decryptString($id);
        $lesson = Lesson::findOrFail($lessonId);
        
        // Get or create quiz
        $quiz = Quiz::firstOrCreate(
            ['lesson_id' => $lesson->id],
            [
                'title' => $lesson->title . ' Quiz',
                'passing_score' => 70,
                'is_active' => true
            ]
        );

        $validated = $request->validate([
            'question_text' => 'required|string',
            'points' => 'required|integer|min:1',
            'answers' => 'required|array|min:4|max:4',
            'answers.*.text' => 'required|string',
            'answers.*.explanation' => 'nullable|string',
            'correct_answer' => 'required|in:A,B,C,D'
        ]);

        DB::beginTransaction();
        try {
            // Get next order number
            $maxOrder = Question::where('quiz_id', $quiz->id)->max('order') ?? 0;

            // Create question
            $question = Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => $validated['question_text'],
                'points' => $validated['points'],
                'order' => $maxOrder + 1
            ]);

            // Create answers
            foreach ($validated['answers'] as $letter => $answerData) {
                Answer::create([
                    'question_id' => $question->id,
                    'option_letter' => $letter,
                    'answer_text' => $answerData['text'],
                    'explanation' => $answerData['explanation'] ?? '',
                    'is_correct' => $letter === $validated['correct_answer']
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Question added successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function editQuestion($id)
    {
        $question = Question::with('answers')->findOrFail($id);
        return response()->json($question);
    }

    public function updateQuestion(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $validated = $request->validate([
            'question_text' => 'required|string',
            'points' => 'required|integer|min:1',
            'answers' => 'required|array|min:4|max:4',
            'answers.*.text' => 'required|string',
            'answers.*.explanation' => 'nullable|string',
            'correct_answer' => 'required|in:A,B,C,D'
        ]);

        DB::beginTransaction();
        try {
            // Update question
            $question->update([
                'question_text' => $validated['question_text'],
                'points' => $validated['points']
            ]);

            // Update answers
            foreach ($validated['answers'] as $letter => $answerData) {
                Answer::updateOrCreate(
                    [
                        'question_id' => $question->id,
                        'option_letter' => $letter
                    ],
                    [
                        'answer_text' => $answerData['text'],
                        'explanation' => $answerData['explanation'] ?? '',
                        'is_correct' => $letter === $validated['correct_answer']
                    ]
                );
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Question updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a question
     */
    public function destroyQuestion($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        return response()->json(['success' => true, 'message' => 'Question deleted successfully']);
    }

    /**
     * Reorder questions
     */
    public function reorderQuestions(Request $request)
    {
        $questionId = $request->input('question_id');
        $direction = $request->input('direction');

        DB::beginTransaction();
        try {
            $question = Question::findOrFail($questionId);
            $currentOrder = $question->order;
            
            if ($direction === 'up') {
                // Find the question above
                $previousQuestion = Question::where('quiz_id', $question->quiz_id)
                    ->where('order', '<', $currentOrder)
                    ->orderBy('order', 'desc')
                    ->first();
                
                if ($previousQuestion) {
                    // Swap orders
                    $question->order = $previousQuestion->order;
                    $previousQuestion->order = $currentOrder;
                    
                    $question->save();
                    $previousQuestion->save();
                }
            } elseif ($direction === 'down') {
                // Find the question below
                $nextQuestion = Question::where('quiz_id', $question->quiz_id)
                    ->where('order', '>', $currentOrder)
                    ->orderBy('order', 'asc')
                    ->first();
                
                if ($nextQuestion) {
                    // Swap orders
                    $question->order = $nextQuestion->order;
                    $nextQuestion->order = $currentOrder;
                    
                    $question->save();
                    $nextQuestion->save();
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Question reordered successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
