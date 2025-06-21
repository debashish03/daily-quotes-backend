<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteCategory;
use App\Models\QuoteShare;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Get dashboard overview
     */
    public function index(): JsonResponse
    {
        $stats = [
            'total_quotes' => Quote::count(),
            'published_quotes' => Quote::where('is_published', true)->count(),
            'total_categories' => QuoteCategory::count(),
            'active_categories' => QuoteCategory::where('is_active', true)->count(),
            'total_users' => User::count(),
            'users_with_preferences' => UserPreference::count(),
            'total_views' => Quote::sum('view_count'),
            'total_shares' => Quote::sum('share_count'),
        ];

        // Recent activity
        $recentQuotes = Quote::with('category')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentShares = QuoteShare::with(['user', 'quote'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Top performing quotes
        $topQuotes = Quote::with('category')
            ->orderBy('view_count', 'desc')
            ->limit(5)
            ->get(['id', 'content', 'author', 'view_count', 'share_count', 'category_id']);

        // Share platform statistics
        $platformStats = QuoteShare::select('platform', DB::raw('COUNT(*) as count'))
            ->groupBy('platform')
            ->orderBy('count', 'desc')
            ->get();

        // Daily quote views (last 7 days)
        $dailyViews = Quote::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(view_count) as views'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'recent_quotes' => $recentQuotes,
                'recent_shares' => $recentShares,
                'top_quotes' => $topQuotes,
                'platform_stats' => $platformStats,
                'daily_views' => $dailyViews
            ]
        ]);
    }

    /**
     * Get comprehensive analytics data
     */
    public function analytics(): JsonResponse
    {
        $analytics = [
            'total_quotes' => Quote::count(),
            'total_users' => User::count(),
            'total_categories' => QuoteCategory::count(),
            'total_shares' => QuoteShare::count(),
            'total_views' => Quote::sum('view_count'),
            'popular_categories' => $this->getPopularCategories(),
            'recent_activity' => $this->getRecentActivity(),
            'user_growth' => $this->getUserGrowthData(),
            'quotes_growth' => $this->calculateGrowthPercentage('quotes'),
            'users_growth' => $this->calculateGrowthPercentage('users'),
            'views_growth' => $this->calculateGrowthPercentage('views'),
            'shares_growth' => $this->calculateGrowthPercentage('shares'),
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Get popular categories
     */
    private function getPopularCategories()
    {
        return QuoteCategory::withCount('quotes')
            ->orderBy('quotes_count', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get recent activity
     */
    private function getRecentActivity()
    {
        $activities = [];

        // Recent quotes
        $recentQuotes = Quote::with('category')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($recentQuotes as $quote) {
            $activities[] = [
                'description' => "New quote added: \"{$quote->content}\"",
                'date' => $quote->created_at->format('M j, Y')
            ];
        }

        // Recent users
        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($recentUsers as $user) {
            $activities[] = [
                'description' => "New user registered: {$user->name}",
                'date' => $user->created_at->format('M j, Y')
            ];
        }

        // Recent shares
        $recentShares = QuoteShare::with(['user', 'quote'])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($recentShares as $share) {
            $activities[] = [
                'description' => "Quote shared on {$share->platform}",
                'date' => $share->created_at->format('M j, Y')
            ];
        }

        // Sort by date and return top 10
        usort($activities, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($activities, 0, 10);
    }

    /**
     * Get user engagement analytics
     */
    public function userAnalytics(): JsonResponse
    {
        $analytics = [
            'total_users' => User::count(),
            'active_users' => UserPreference::where('notifications_enabled', true)->count(),
            'users_with_device_tokens' => UserPreference::whereNotNull('device_token')->count(),
            'popular_notification_times' => UserPreference::select('notification_time', DB::raw('COUNT(*) as count'))
                ->groupBy('notification_time')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get(),
            'category_preferences' => $this->getCategoryPreferences(),
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Get quote performance analytics
     */
    public function quoteAnalytics(): JsonResponse
    {
        $analytics = [
            'total_quotes' => Quote::count(),
            'published_quotes' => Quote::where('is_published', true)->count(),
            'draft_quotes' => Quote::where('is_published', false)->count(),
            'scheduled_quotes' => Quote::whereNotNull('scheduled_date')->count(),
            'total_views' => Quote::sum('view_count'),
            'total_shares' => Quote::sum('share_count'),
            'average_views_per_quote' => round(Quote::avg('view_count'), 1),
            'average_shares_per_quote' => round(Quote::avg('share_count'), 1),
            'top_performing_categories' => $this->getTopPerformingCategories(),
            'top_performing_quotes' => $this->getTopPerformingQuotes(),
            'quotes_by_category' => $this->getQuotesByCategory(),
            'views_trend' => $this->getViewsTrend(),
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    /**
     * Get top performing quotes
     */
    private function getTopPerformingQuotes()
    {
        return Quote::with('category')
            ->orderBy('view_count', 'desc')
            ->limit(10)
            ->get(['id', 'content', 'author', 'view_count', 'share_count', 'category_id', 'created_at']);
    }

    /**
     * Get quotes distribution by category
     */
    private function getQuotesByCategory()
    {
        return QuoteCategory::withCount('quotes')
            ->withSum('quotes', 'view_count')
            ->orderBy('quotes_count', 'desc')
            ->get();
    }

    /**
     * Get views trend over time
     */
    private function getViewsTrend()
    {
        $days = 30;
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $views = Quote::whereDate('created_at', $date)->sum('view_count');

            $data[] = [
                'date' => now()->subDays($i)->format('M j'),
                'views' => $views
            ];
        }

        return $data;
    }

    private function getCategoryPreferences()
    {
        $preferences = UserPreference::whereNotNull('preferred_categories')->get();
        $categoryCounts = [];

        foreach ($preferences as $preference) {
            if (is_array($preference->preferred_categories)) {
                foreach ($preference->preferred_categories as $categoryId) {
                    $categoryCounts[$categoryId] = ($categoryCounts[$categoryId] ?? 0) + 1;
                }
            }
        }

        $categories = QuoteCategory::whereIn('id', array_keys($categoryCounts))->get();
        $result = [];

        foreach ($categories as $category) {
            $result[] = [
                'category' => $category,
                'user_count' => $categoryCounts[$category->id] ?? 0
            ];
        }

        return collect($result)->sortByDesc('user_count')->values();
    }

    private function getTopPerformingCategories()
    {
        return QuoteCategory::withCount('quotes')
            ->withSum('quotes', 'view_count')
            ->withSum('quotes', 'share_count')
            ->orderBy('quotes_sum_view_count', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get all users with their preferences
     */
    public function users(): JsonResponse
    {
        $users = User::with('preferences')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total_users' => User::count(),
            'active_users' => UserPreference::where('notifications_enabled', true)->count(),
            'users_with_preferences' => UserPreference::where(function ($query) {
                $query->where('notifications_enabled', true)
                    ->orWhereNotNull('device_token')
                    ->orWhereJsonLength('preferred_categories', '>', 0);
            })->count(),
            'users_with_device_tokens' => UserPreference::whereNotNull('device_token')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'users' => $users,
                'stats' => $stats
            ]
        ]);
    }

    /**
     * Get user detail with preferences
     */
    public function userDetail(string $id): JsonResponse
    {
        try {
            Log::info('DashboardController::userDetail called', [
                'user_id' => $id,
                'authenticated_user' => Auth::check() ? Auth::user()->id : 'not_authenticated',
                'request_url' => request()->url(),
                'request_method' => request()->method()
            ]);

            $user = User::with('preferences')->findOrFail($id);

            Log::info('User found successfully', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'has_preferences' => $user->preferences ? 'yes' : 'no'
            ]);

            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching user detail', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'User not found or error occurred',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update user and preferences
     */
    public function updateUser(Request $request, string $id): JsonResponse
    {
        Log::info('DashboardController::updateUser called', [
            'user_id' => $id,
            'request_data' => $request->all()
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'is_admin' => 'boolean',
            'notification_time' => 'nullable|string',
            'notifications_enabled' => 'boolean',
            'preferred_categories' => 'nullable|array',
            'preferred_categories.*' => 'integer|exists:quote_categories,id'
        ]);

        $user = User::findOrFail($id);

        // Update user
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_admin' => $request->is_admin ?? false
        ]);

        // Update or create preferences
        $preferences = $user->preferences;
        if (!$preferences) {
            $preferences = new UserPreference();
            $preferences->user_id = $user->id;
            Log::info('Creating new UserPreference for user', ['user_id' => $user->id]);
        } else {
            Log::info('Updating existing UserPreference for user', ['user_id' => $user->id]);
        }

        // Handle notification_time - use default if null
        $notificationTime = $request->notification_time;
        if (empty($notificationTime)) {
            $notificationTime = '09:00:00'; // Default value from migration
            Log::info('Using default notification time', ['default_time' => $notificationTime]);
        } else {
            Log::info('Using provided notification time', ['provided_time' => $notificationTime]);
        }

        $preferences->notification_time = $notificationTime;
        $preferences->notifications_enabled = $request->notifications_enabled ?? false;
        $preferences->preferred_categories = $request->preferred_categories;

        Log::info('Saving UserPreference', [
            'user_id' => $user->id,
            'notification_time' => $preferences->notification_time,
            'notifications_enabled' => $preferences->notifications_enabled,
            'preferred_categories' => $preferences->preferred_categories
        ]);

        // Check if preferences are meaningful (has categories, notifications enabled, or device token)
        $hasMeaningfulPreferences = $preferences->notifications_enabled ||
            !empty($preferences->device_token) ||
            (!empty($preferences->preferred_categories) && count($preferences->preferred_categories) > 0);

        if ($hasMeaningfulPreferences) {
            $preferences->save();
            Log::info('UserPreference saved successfully', ['user_id' => $user->id]);
        } else {
            // Delete the preference record if it has no meaningful preferences
            if ($preferences->exists) {
                $preferences->delete();
                Log::info('UserPreference deleted (no meaningful preferences)', ['user_id' => $user->id]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $user->load('preferences'),
            'message' => 'User updated successfully'
        ]);
    }

    /**
     * Export users to CSV
     */
    public function exportUsers(): \Symfony\Component\HttpFoundation\Response
    {
        $users = User::with('preferences')->get();

        $filename = 'users-export-' . date('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, ['ID', 'Name', 'Email', 'Joined Date', 'Is Admin', 'Has Preferences', 'Notification Time', 'Device Token']);

            // Add user data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->created_at->format('Y-m-d H:i:s'),
                    $user->is_admin ? 'Yes' : 'No',
                    $user->preferences ? 'Yes' : 'No',
                    $user->preferences ? $user->preferences->notification_time : '',
                    $user->preferences ? ($user->preferences->device_token ? 'Yes' : 'No') : 'No'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get user growth data for charts
     */
    private function getUserGrowthData()
    {
        $days = 30;
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = User::whereDate('created_at', $date)->count();

            $data[] = [
                'date' => now()->subDays($i)->format('M j'),
                'count' => $count
            ];
        }

        return $data;
    }

    /**
     * Calculate growth percentage for metrics
     */
    private function calculateGrowthPercentage($metric)
    {
        $currentPeriod = now()->subDays(30);
        $previousPeriod = $currentPeriod->copy()->subDays(30);

        switch ($metric) {
            case 'quotes':
                $current = Quote::where('created_at', '>=', $currentPeriod)->count();
                $previous = Quote::whereBetween('created_at', [$previousPeriod, $currentPeriod])->count();
                break;
            case 'users':
                $current = User::where('created_at', '>=', $currentPeriod)->count();
                $previous = User::whereBetween('created_at', [$previousPeriod, $currentPeriod])->count();
                break;
            case 'views':
                $current = Quote::where('created_at', '>=', $currentPeriod)->sum('view_count');
                $previous = Quote::whereBetween('created_at', [$previousPeriod, $currentPeriod])->sum('view_count');
                break;
            case 'shares':
                $current = QuoteShare::where('created_at', '>=', $currentPeriod)->count();
                $previous = QuoteShare::whereBetween('created_at', [$previousPeriod, $currentPeriod])->count();
                break;
            default:
                return 0;
        }

        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
