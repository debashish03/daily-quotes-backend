<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QuoteController extends Controller
{
    /**
     * Display a listing of quotes
     */
    public function index(Request $request): JsonResponse
    {
        $query = Quote::with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('status')) {
            $query->where('is_published', $request->status === 'published');
        }

        $quotes = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $quotes
        ]);
    }

    /**
     * Store a newly created quote
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'author' => 'nullable|string|max:255',
            'category_id' => 'required|exists:quote_categories,id',
            'scheduled_date' => 'nullable|date',
            'is_published' => 'boolean'
        ]);

        $quote = Quote::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $quote->load('category'),
            'message' => 'Quote created successfully'
        ], 201);
    }

    /**
     * Display the specified quote
     */
    public function show(string $id): JsonResponse
    {
        $quote = Quote::with('category')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $quote
        ]);
    }

    /**
     * Update the specified quote
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'author' => 'nullable|string|max:255',
            'category_id' => 'required|exists:quote_categories,id',
            'scheduled_date' => 'nullable|date',
            'is_published' => 'boolean'
        ]);

        $quote = Quote::findOrFail($id);
        $quote->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $quote->load('category'),
            'message' => 'Quote updated successfully'
        ]);
    }

    /**
     * Remove the specified quote
     */
    public function destroy(string $id): JsonResponse
    {
        $quote = Quote::findOrFail($id);
        $quote->delete();

        return response()->json([
            'success' => true,
            'message' => 'Quote deleted successfully'
        ]);
    }

    /**
     * Get categories for dropdown
     */
    public function categories(): JsonResponse
    {
        $categories = QuoteCategory::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get quote statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_quotes' => Quote::count(),
            'published_quotes' => Quote::where('is_published', true)->count(),
            'draft_quotes' => Quote::where('is_published', false)->count(),
            'total_views' => Quote::sum('view_count'),
            'total_shares' => Quote::sum('share_count'),
            'top_quotes' => Quote::orderBy('view_count', 'desc')->limit(5)->get(['id', 'content', 'view_count', 'share_count'])
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
