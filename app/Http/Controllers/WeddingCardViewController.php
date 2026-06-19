<?php

namespace App\Http\Controllers;

use App\Models\WeddingCard;
use App\Models\CardAnalytic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeddingCardViewController extends Controller
{
    /**
     * Show the wedding card view to guests.
     */
    public function show(Request $request, $unique_url)
    {
        $card = WeddingCard::where('unique_url', $unique_url)
            ->where('is_published', true)
            ->with('designTemplate')
            ->firstOrFail();
        
        // Check if the card has expired
        if ($card->isExpired()) {
            return view('wedding-card.expired', compact('card'));
        }
        
        // Track view only if the viewer is not the card owner
        $this->trackView($card, $request);
        
        // Fetch RSVP messages for the card (only messages that are not empty)
        $rsvpMessages = $card->rsvps()
            ->whereNotNull('message')
            ->where('message', '!=', '')
            ->orderBy('created_at', 'desc')
            ->get(['guest_name', 'message', 'created_at']);
        
        return view('wedding-card.view', compact('card', 'rsvpMessages'));
    }
    
    /**
     * Track card view if the viewer is not the card owner.
     */
    private function trackView(WeddingCard $card, Request $request)
    {
        // Check if user is authenticated and if they are the card owner
        if (Auth::check()) {
            $currentUser = Auth::user();
            // Don't track if the current user is the card owner
            if ($currentUser->id === $card->user_id) {
                return;
            }
        }
        
        // Track the view since it's either an unauthenticated user or not the card owner
        CardAnalytic::track($card->id, 'view');
    }
} 