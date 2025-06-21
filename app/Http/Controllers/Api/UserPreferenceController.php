<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller
{
    /**
     * Get user preferences
     */
    public function index(): JsonResponse
    {
        $preferences = Auth::user()->preferences()->first();

        if (!$preferences) {
            $preferences = Auth::user()->preferences()->create([
                'notification_time' => '09:00:00',
                'notifications_enabled' => true
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $preferences
        ]);
    }

    /**
     * Update user preferences
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'notification_time' => 'required|date_format:H:i:s',
            'preferred_categories' => 'nullable|array',
            'preferred_categories.*' => 'integer|exists:quote_categories,id',
            'notifications_enabled' => 'boolean'
        ]);

        $user = Auth::user();
        $preferences = $user->preferences()->first();

        if (!$preferences) {
            $preferences = $user->preferences()->create([
                'notification_time' => $request->notification_time,
                'preferred_categories' => $request->preferred_categories,
                'notifications_enabled' => $request->notifications_enabled ?? true
            ]);
        } else {
            $preferences->update([
                'notification_time' => $request->notification_time,
                'preferred_categories' => $request->preferred_categories,
                'notifications_enabled' => $request->notifications_enabled ?? $preferences->notifications_enabled
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $preferences,
            'message' => 'Preferences updated successfully'
        ]);
    }

    /**
     * Update device token for push notifications
     */
    public function updateDeviceToken(Request $request): JsonResponse
    {
        $request->validate([
            'device_token' => 'required|string'
        ]);

        $user = Auth::user();
        $preferences = $user->preferences()->first();

        if (!$preferences) {
            $preferences = $user->preferences()->create([
                'device_token' => $request->device_token,
                'notification_time' => '09:00:00',
                'notifications_enabled' => true
            ]);
        } else {
            $preferences->update([
                'device_token' => $request->device_token
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Device token updated successfully'
        ]);
    }
}
