<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\QuoteResource;

class QuoteController extends Controller
{
    /**
     * Get today's quote
     */
    public function today(): JsonResponse
    {
        $quote = Quote::where('is_published', true)
            ->where('scheduled_date', today())
            ->with('category')
            ->first();

        if (!$quote) {
            // Fallback to a random published quote
            $quote = Quote::where('is_published', true)
                ->with('category')
                ->inRandomOrder()
                ->first();
        }

        if ($quote) {
            // Increment view count
            $quote->increment('view_count');
        }

        return response()->json([
            'success' => true,
            'data' => $quote
        ]);
    }

    /**
     * Get random quote by category
     */
    public function random(Request $request): JsonResponse
    {
        $categoryId = $request->get('category_id');

        $query = Quote::where('is_published', true)->with('category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $quote = $query->inRandomOrder()->first();

        if ($quote) {
            $quote->increment('view_count');
        }

        return response()->json([
            'success' => true,
            'data' => $quote
        ]);
    }

    /**
     * Get quotes by category
     */
    public function byCategory(Request $request, string $categorySlug): JsonResponse
    {
        $category = QuoteCategory::where('slug', $categorySlug)
            ->where('is_active', true)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found or is not active'
            ], 404);
        }

        $quotes = $category->quotes()->where('is_active', true)
            ->inRandomOrder()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => [
                'category' => new CategoryResource($category),
                'quotes' => QuoteResource::collection($quotes)
            ]
        ]);
    }

    /**
     * Get all categories
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
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $quote = Quote::with('category')->findOrFail($id);

        if ($quote->is_published) {
            $quote->increment('view_count');
        }

        return response()->json([
            'success' => true,
            'data' => $quote
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
