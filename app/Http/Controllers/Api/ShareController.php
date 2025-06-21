<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteShare;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ShareController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Track quote share
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'quote_id' => 'required|exists:quotes,id',
            'platform' => 'required|string|in:whatsapp,instagram,twitter,facebook,telegram,email,other'
        ]);

        $user = Auth::user();
        $quote = Quote::findOrFail($request->quote_id);

        // Create share record
        QuoteShare::create([
            'user_id' => $user->id,
            'quote_id' => $quote->id,
            'platform' => $request->platform,
            'share_data' => $request->only(['platform', 'timestamp'])
        ]);

        // Increment quote share count
        $quote->increment('share_count');

        return response()->json([
            'success' => true,
            'message' => 'Share tracked successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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

    /**
     * Get user's share history
     */
    public function history(): JsonResponse
    {
        $shares = Auth::user()->quoteShares()
            ->with('quote.category')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $shares
        ]);
    }

    /**
     * Get share statistics for a quote
     */
    public function statistics(string $quoteId): JsonResponse
    {
        $quote = Quote::findOrFail($quoteId);

        $shareStats = QuoteShare::where('quote_id', $quoteId)
            ->selectRaw('platform, COUNT(*) as count')
            ->groupBy('platform')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'quote' => $quote,
                'share_statistics' => $shareStats,
                'total_shares' => $quote->share_count
            ]
        ]);
    }
}
