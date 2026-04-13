<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Scenario;
use App\Models\ScenarioItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class ScenarioController extends Controller
{
    /**
     * Show scenario management page
     */
    public function index()
    {
        $lessons = Lesson::where('has_simulation', true)->get();
        return view('admin.scenarios.index', compact('lessons'));
    }

    /**
     * Get scenarios data for DataTable
     */
    public function getData(Request $request)
    {
        $scenarios = Scenario::with('lesson')->withCount('items');

        if ($request->type) {
            $scenarios->where('type', $request->type);
        }

        if ($request->lesson_id) {
            $scenarios->where('lesson_id', $request->lesson_id);
        }

        return DataTables::of($scenarios)
            ->editColumn('id', function ($row) {
                return Crypt::encryptString($row->id);
            })
            ->addColumn('lesson_title', function ($row) {
                return $row->lesson->title;
            })
            ->addColumn('type_badge', function ($row) {
                return match($row->type) {
                    'pre_assessment' => '<span class="badge bg-label-info">Pre-Assessment</span>',
                    'post_assessment' => '<span class="badge bg-label-warning">Post-Assessment</span>',
                    'simulation' => '<span class="badge bg-label-primary">Simulation</span>',
                };
            })
            ->rawColumns(['type_badge'])
            ->make(true);
    }

    /**
     * Store a new scenario
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:pre_assessment,post_assessment,simulation',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        $validated['order'] = Scenario::where('lesson_id', $validated['lesson_id'])->max('order') + 1;

        Scenario::create($validated);

        return response()->json(['success' => true, 'message' => 'Scenario created successfully.']);
    }

    /**
     * Get data for editing
     */
    public function edit($id)
    {
        $decrypted = Crypt::decryptString($id);
        $scenario = Scenario::findOrFail($decrypted);

        return response()->json([
            'id' => Crypt::encryptString($scenario->id),
            'lesson_id' => $scenario->lesson_id,
            'title' => $scenario->title,
            'description' => $scenario->description,
            'type' => $scenario->type,
        ]);
    }

    /**
     * Update a scenario
     */
    public function update(Request $request, $id)
    {
        $decrypted = Crypt::decryptString($id);
        $scenario = Scenario::findOrFail($decrypted);

        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:pre_assessment,post_assessment,simulation',
        ]);

        $scenario->update($validated);

        return response()->json(['success' => true, 'message' => 'Scenario updated successfully.']);
    }

    /**
     * Delete a scenario
     */
    public function destroy($id)
    {
        $decrypted = Crypt::decryptString($id);
        $scenario = Scenario::findOrFail($decrypted);
        $scenario->delete();

        return response()->json(['success' => true, 'message' => 'Scenario deleted successfully.']);
    }

    /**
     * Toggle active status
     */
    public function toggle($id)
    {
        $decrypted = Crypt::decryptString($id);
        $scenario = Scenario::findOrFail($decrypted);
        $scenario->update(['is_active' => !$scenario->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated.',
            'is_active' => $scenario->is_active
        ]);
    }

    // ========== SCENARIO ITEMS ==========

    /**
     * Show items management for a scenario
     */
    public function items($id)
    {
        $decrypted = Crypt::decryptString($id);
        $scenario = Scenario::with('lesson')->findOrFail($decrypted);

        return view('admin.scenarios.items', compact('scenario'));
    }

    /**
     * Get items data for DataTable
     */
    public function getItemsData(Request $request, $id)
    {
        $decrypted = Crypt::decryptString($id);
        $items = ScenarioItem::where('scenario_id', $decrypted)->orderBy('order');

        return DataTables::of($items)
            ->editColumn('id', function ($row) {
                return Crypt::encryptString($row->id);
            })
            ->addColumn('options_count', function ($row) {
                return $row->options ? count($row->options) : 0;
            })
            ->make(true);
    }

    /**
     * Store a scenario item
     */
    public function storeItem(Request $request, $id)
    {
        $decrypted = Crypt::decryptString($id);
        $scenario = Scenario::findOrFail($decrypted);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_option' => 'required|integer|min:0',
        ]);

        // Handle image upload
        $imagePath = $request->file('image')->store('simulations', 'public');

        // Build options array with correct answer marker
        $options = [];
        foreach ($validated['options'] as $index => $text) {
            $options[] = [
                'text' => $text,
                'is_correct' => (int)$validated['correct_option'] === $index,
            ];
        }

        ScenarioItem::create([
            'scenario_id' => $decrypted,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'],
            'image_path' => $imagePath,
            'options' => $options,
            'order' => ScenarioItem::where('scenario_id', $decrypted)->max('order') + 1,
        ]);

        return response()->json(['success' => true, 'message' => 'Item added successfully.']);
    }

    /**
     * Get item data for editing
     */
    public function editItem($id)
    {
        $decrypted = Crypt::decryptString($id);
        $item = ScenarioItem::findOrFail($decrypted);

        return response()->json([
            'id' => Crypt::encryptString($item->id),
            'title' => $item->title,
            'description' => $item->description,
            'content' => $item->content,
            'correct_action' => $item->correct_action,
            'options' => $item->options ?? [],
            'hints' => $item->hints ?? [],
        ]);
    }

    /**
     * Update a scenario item
     */
    public function updateItem(Request $request, $id)
    {
        $decrypted = Crypt::decryptString($id);
        $item = ScenarioItem::findOrFail($decrypted);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'correct_action' => 'nullable|string|max:255',
            'options' => 'nullable|array',
            'options.*' => 'string',
            'hints' => 'nullable|array',
            'hints.*' => 'string',
        ]);

        $item->update($validated);

        return response()->json(['success' => true, 'message' => 'Item updated successfully.']);
    }

    /**
     * Delete a scenario item
     */
    public function destroyItem($id)
    {
        $decrypted = Crypt::decryptString($id);
        $item = ScenarioItem::findOrFail($decrypted);
        $item->delete();

        return response()->json(['success' => true, 'message' => 'Item deleted successfully.']);
    }

    /**
     * Reorder items
     */
    public function reorderItems(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required',
            'items.*.order' => 'required|integer',
        ]);

        foreach ($validated['items'] as $itemData) {
            $decrypted = Crypt::decryptString($itemData['id']);
            ScenarioItem::where('id', $decrypted)->update(['order' => $itemData['order']]);
        }

        return response()->json(['success' => true, 'message' => 'Items reordered successfully.']);
    }
}
