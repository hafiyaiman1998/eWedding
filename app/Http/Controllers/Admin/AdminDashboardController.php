<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\DesignTemplate;
use App\Models\User;
use App\Models\WeddingCard;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_clients' => User::clients()->count(),
            'total_admins' => User::admins()->count(),
            'total_templates' => DesignTemplate::count(),
            'malaysian_templates' => DesignTemplate::malaysian()->count(),
            'total_cards' => WeddingCard::count(),
            'published_cards' => WeddingCard::published()->count(),
            'recent_clients' => User::clients()->latest()->limit(5)->get(),
            'recent_cards' => WeddingCard::with(['user', 'designTemplate'])
                ->latest()
                ->limit(5)
                ->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function analytics(): View
    {
        $monthlyCards = WeddingCard::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $templateUsage = DesignTemplate::withCount('weddingCards')
            ->orderBy('wedding_cards_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.analytics', compact('monthlyCards', 'templateUsage'));
    }

    public function activity(): View
    {
        $recentActivity = [
            'recent_registrations' => User::clients()->latest()->limit(10)->get(),
            'recent_cards' => WeddingCard::with(['user', 'designTemplate'])
                ->latest()
                ->limit(10)
                ->get(),
            'recent_published' => WeddingCard::published()
                ->with(['user', 'designTemplate'])
                ->latest('updated_at')
                ->limit(10)
                ->get(),
        ];

        return view('admin.activity', compact('recentActivity'));
    }

    /**
     * Show the settings page.
     */
    public function settings(): View
    {
        return view('admin.settings');
    }

    /**
     * Update system settings.
     */
    public function updateSettings(UpdateSettingsRequest $request): JsonResponse
    {
        try {
            // Save settings to database
            \App\Models\Setting::set('max_cards_per_user', $request->max_cards_per_user, 'integer', 'Maximum number of wedding cards each user can create');
            \App\Models\Setting::set('default_card_expiry_days', $request->default_card_expiry, 'integer', 'Default number of days cards remain active');
            \App\Models\Setting::set('allow_custom_domains', $request->boolean('allow_custom_domains') ? '1' : '0', 'boolean', 'Allow users to use custom domains');
            \App\Models\Setting::set('enable_analytics_tracking', $request->boolean('enable_analytics') ? '1' : '0', 'boolean', 'Enable analytics tracking for cards');
            \App\Models\Setting::set('auto_approve_cards', $request->boolean('auto_approve_cards') ? '1' : '0', 'boolean', 'Automatically approve new cards without admin review');

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully!',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating settings: '.$e->getMessage(),
            ], 500);
        }
    }
}
