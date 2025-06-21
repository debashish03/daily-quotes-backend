<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuoteCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index(): JsonResponse
    {
        $categories = QuoteCategory::withCount('quotes')
            ->orderBy('name')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:quote_categories',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-F]{6}$/i',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $category = QuoteCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'color' => $request->color ?? '#667eea',
            'image' => $imagePath,
            'is_active' => $request->is_active ?? true
        ]);

        return response()->json([
            'success' => true,
            'data' => $category->load('quotes'),
            'message' => 'Category created successfully'
        ], 201);
    }

    /**
     * Display the specified category
     */
    public function show(string $id): JsonResponse
    {
        $category = QuoteCategory::with('quotes')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:quote_categories,name,' . $id,
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-F]{6}$/i',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean'
        ]);

        $category = QuoteCategory::findOrFail($id);

        $imagePath = $category->image;
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'color' => $request->color ?? $category->color,
            'image' => $imagePath,
            'is_active' => $request->is_active ?? $category->is_active
        ]);

        return response()->json([
            'success' => true,
            'data' => $category->load('quotes'),
            'message' => 'Category updated successfully'
        ]);
    }

    /**
     * Remove the specified category
     */
    public function destroy(string $id): JsonResponse
    {
        $category = QuoteCategory::findOrFail($id);

        // Check if category has quotes
        if ($category->quotes()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with existing quotes'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }

    /**
     * Get category statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_categories' => QuoteCategory::count(),
            'active_categories' => QuoteCategory::where('is_active', true)->count(),
            'categories_with_quotes' => QuoteCategory::has('quotes')->count(),
            'top_categories' => QuoteCategory::withCount('quotes')
                ->orderBy('quotes_count', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'quotes_count'])
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
