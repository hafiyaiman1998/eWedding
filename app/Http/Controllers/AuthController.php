<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Http\Requests\LoginRequest;
use App\Models\CardAnalytic;
use App\Models\Rsvp;
use App\Models\WeddingCard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        // Try to authenticate user
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Redirect based on user type
            if ($user->isAdmin()) {
                // Login as admin guard as well
                Auth::guard('admin')->login($user, $remember);

                return redirect()->intended('/admin/dashboard');
            } else {
                // Login as user guard as well
                Auth::guard('user')->login($user, $remember);

                return redirect()->intended('/user/dashboard');
            }
        }

        return redirect()->back()
            ->withErrors(['email' => 'Invalid credentials'])
            ->withInput();
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request): RedirectResponse
    {
        // Logout from all guards
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();
        Auth::guard('user')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Redirect authenticated users to the dashboard for their type.
     */
    public function dashboard(): RedirectResponse
    {
        if (Auth::user()->type === UserType::Admin->value) {
            return redirect('/admin/dashboard');
        }

        return redirect('/user/dashboard');
    }

    /**
     * Show admin dashboard
     */
    public function adminDashboard(): View
    {
        return view('admin.dashboard');
    }

    /**
     * Show user dashboard
     */
    public function userDashboard(): View
    {
        $user = Auth::user();
        $cardIds = WeddingCard::where('user_id', $user->id)->pluck('id');

        $stats = [
            'total_cards' => WeddingCard::where('user_id', $user->id)->count(),
            'published_cards' => WeddingCard::where('user_id', $user->id)->where('is_published', true)->count(),
            'draft_cards' => WeddingCard::where('user_id', $user->id)->where('is_published', false)->count(),
            'total_views' => CardAnalytic::whereIn('wedding_card_id', $cardIds)->where('event_type', 'view')->count(),
            'total_rsvps' => Rsvp::whereIn('wedding_card_id', $cardIds)->count(),
            'attending_rsvps' => Rsvp::whereIn('wedding_card_id', $cardIds)->where('attendance_status', 'yes')->count(),
            'recent_cards' => WeddingCard::where('user_id', $user->id)->with('designTemplate')->latest()->limit(5)->get(),
            'recent_rsvps' => Rsvp::whereIn('wedding_card_id', $cardIds)->latest()->limit(5)->get(),
        ];

        return view('user.dashboard', compact('stats'));
    }
}
