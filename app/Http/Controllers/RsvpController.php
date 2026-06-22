<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckEmailRequest;
use App\Http\Requests\StoreRsvpRequest;
use App\Http\Requests\UpdateRsvpRequest;
use App\Models\CardAnalytic;
use App\Models\Rsvp;
use App\Models\WeddingCard;
use Illuminate\Http\JsonResponse;

class RsvpController extends Controller
{
    /**
     * Store a new RSVP response for a wedding card.
     */
    public function store(StoreRsvpRequest $request, string $unique_url): JsonResponse
    {
        // Find the wedding card
        $card = WeddingCard::where('unique_url', $unique_url)
            ->where('is_published', true)
            ->firstOrFail();

        $validated = $request->validated();

        // Check if this email has already RSVPed for this card
        $existingRsvp = Rsvp::where('wedding_card_id', $card->id)
            ->where('guest_email', $validated['guest_email'])
            ->first();

        if ($existingRsvp) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted an RSVP for this wedding. If you need to make changes, please contact the couple directly.',
            ], 422);
        }

        // Set default number of guests if not provided
        if (! isset($validated['number_of_guests'])) {
            $validated['number_of_guests'] = $validated['attendance_status'] === 'yes' ? 1 : 0;
        }

        // Create the RSVP
        $rsvp = Rsvp::create([
            'wedding_card_id' => $card->id,
            'guest_name' => $validated['guest_name'],
            'guest_email' => $validated['guest_email'],
            'guest_phone' => $validated['guest_phone'],
            'attendance_status' => $validated['attendance_status'],
            'number_of_guests' => $validated['number_of_guests'],
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Track analytics
        CardAnalytic::create([
            'wedding_card_id' => $card->id,
            'event_type' => $validated['attendance_status'] === 'yes' ? 'rsvp_yes' : 'rsvp_no',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'metadata' => [
                'guest_name' => $validated['guest_name'],
                'number_of_guests' => $validated['number_of_guests'],
                'has_message' => ! empty($validated['message']),
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => $validated['attendance_status'] === 'yes'
                ? 'Thank you for your RSVP! We look forward to celebrating with you.'
                : 'Thank you for letting us know. We\'ll miss you on our special day!',
            'rsvp' => [
                'id' => $rsvp->id,
                'guest_name' => $rsvp->guest_name,
                'attendance_status' => $rsvp->attendance_status,
                'number_of_guests' => $rsvp->number_of_guests,
            ],
        ]);
    }

    /**
     * Update an existing RSVP (for rare cases where changes are needed).
     */
    public function update(UpdateRsvpRequest $request, string $unique_url, Rsvp $rsvp): JsonResponse
    {
        // Find the wedding card
        $card = WeddingCard::where('unique_url', $unique_url)
            ->where('is_published', true)
            ->firstOrFail();

        // Ensure the RSVP belongs to this card
        if ($rsvp->wedding_card_id !== $card->id) {
            abort(404);
        }

        $validated = $request->validated();

        // Set default number of guests if not provided
        if (! isset($validated['number_of_guests'])) {
            $validated['number_of_guests'] = $validated['attendance_status'] === 'yes' ? 1 : 0;
        }

        // Update the RSVP
        $rsvp->update([
            'guest_name' => $validated['guest_name'],
            'guest_phone' => $validated['guest_phone'],
            'attendance_status' => $validated['attendance_status'],
            'number_of_guests' => $validated['number_of_guests'],
            'message' => $validated['message'],
        ]);

        // Track analytics for the update
        CardAnalytic::create([
            'wedding_card_id' => $card->id,
            'event_type' => 'rsvp_updated',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'metadata' => [
                'guest_name' => $validated['guest_name'],
                'new_attendance_status' => $validated['attendance_status'],
                'number_of_guests' => $validated['number_of_guests'],
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your RSVP has been updated successfully.',
            'rsvp' => [
                'id' => $rsvp->id,
                'guest_name' => $rsvp->guest_name,
                'attendance_status' => $rsvp->attendance_status,
                'number_of_guests' => $rsvp->number_of_guests,
            ],
        ]);
    }

    /**
     * Get RSVP status for a specific email (to check if already submitted).
     */
    public function checkEmail(CheckEmailRequest $request, string $unique_url): JsonResponse
    {
        $card = WeddingCard::where('unique_url', $unique_url)
            ->where('is_published', true)
            ->firstOrFail();

        $rsvp = Rsvp::where('wedding_card_id', $card->id)
            ->where('guest_email', $request->email)
            ->first();

        return response()->json([
            'has_rsvp' => (bool) $rsvp,
            'rsvp' => $rsvp ? [
                'id' => $rsvp->id,
                'guest_name' => $rsvp->guest_name,
                'attendance_status' => $rsvp->attendance_status,
                'number_of_guests' => $rsvp->number_of_guests,
                'submitted_at' => $rsvp->created_at->format('M d, Y \a\t h:i A'),
            ] : null,
        ]);
    }
}
