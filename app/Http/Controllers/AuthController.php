<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\WeddingCard;
use App\Models\CardAnalytic;
use App\Models\Rsvp;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        // Try to authenticate user
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Redirect based on user type
            if ($user->type === 'admin') {
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
    public function logout(Request $request)
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
     * Show admin dashboard
     */
    public function adminDashboard()
    {
        return view('admin.dashboard');
    }

    /**
     * Show user dashboard
     */
    public function userDashboard()
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