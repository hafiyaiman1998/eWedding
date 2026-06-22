<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectCardRequest;
use App\Http\Requests\UpdateAdminCardRequest;
use App\Models\DesignTemplate;
use App\Models\User;
use App\Models\WeddingCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCardController extends Controller
{
    /**
     * Display a listing of all wedding cards.
     */
    public function index(Request $request): View
    {
        $query = WeddingCard::with(['user', 'designTemplate']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('designTemplate', function ($templateQuery) use ($search) {
                        $templateQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'published') {
                $query->published();
            } elseif ($status === 'draft') {
                $query->where('is_published', false);
            }
        }

        // Filter by template
        if ($request->filled('template')) {
            $query->where('design_template_id', $request->get('template'));
        }

        // Filter by user
        if ($request->filled('user')) {
            $query->where('user_id', $request->get('user'));
        }

        $cards = $query->latest()->paginate(15);
        $templates = DesignTemplate::orderBy('name')->get();
        $users = User::clients()->orderBy('name')->get();

        return view('admin.cards.index', compact('cards', 'templates', 'users'));
    }

    /**
     * Display only published wedding cards.
     */
    public function published(Request $request): View
    {
        $query = WeddingCard::published()->with(['user', 'designTemplate', 'analytics', 'rsvps']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $cards = $query->latest('updated_at')->paginate(15);

        return view('admin.cards.published', compact('cards'));
    }

    /**
     * Display the specified wedding card.
     */
    public function show(WeddingCard $card): View
    {
        $card->load(['user', 'designTemplate', 'analytics', 'rsvps']);

        return view('admin.cards.show', compact('card'));
    }

    /**
     * Show the form for editing the specified wedding card.
     */
    public function edit(WeddingCard $card): View
    {
        $card->load(['user', 'designTemplate']);
        $templates = DesignTemplate::active()->orderBy('name')->get();

        return view('admin.cards.edit', compact('card', 'templates'));
    }

    /**
     * Update the specified wedding card in storage.
     */
    public function update(UpdateAdminCardRequest $request, WeddingCard $card): RedirectResponse
    {
        $validated = $request->validated();

        // Prepare card details
        $cardDetails = [
            'bride_name' => $validated['bride_name'],
            'groom_name' => $validated['groom_name'],
            'wedding_date' => $validated['wedding_date'],
            'wedding_time' => $validated['wedding_time'] ?? '',
            'venue' => $validated['venue'] ?? '',
            'address' => $validated['address'] ?? '',
            'contact_bride' => $validated['contact_bride'] ?? '',
            'contact_groom' => $validated['contact_groom'] ?? '',
        ];

        $card->update([
            'title' => $validated['title'],
            'design_template_id' => $validated['design_template_id'],
            'custom_message' => $validated['custom_message'],
            'is_published' => $request->boolean('is_published'),
            'card_details' => $cardDetails,
        ]);

        return redirect()->route('admin.cards.index')
            ->with('success', 'Wedding card updated successfully.');
    }

    /**
     * Remove the specified wedding card from storage.
     */
    public function destroy(WeddingCard $card): RedirectResponse
    {
        $cardTitle = $card->title;
        $card->delete();

        return redirect()->route('admin.cards.index')
            ->with('success', "Wedding card '{$cardTitle}' has been deleted successfully.");
    }

    /**
     * Toggle the published status of a wedding card.
     */
    public function togglePublished(WeddingCard $card): RedirectResponse
    {
        $card->update([
            'is_published' => ! $card->is_published,
        ]);

        $status = $card->is_published ? 'published' : 'unpublished';

        return redirect()->back()
            ->with('success', "Wedding card has been {$status} successfully.");
    }

    /**
     * Preview the wedding card.
     */
    public function preview(WeddingCard $card): View
    {
        $card->load(['user', 'designTemplate']);

        return view('admin.cards.preview', compact('card'));
    }

    /**
     * Display pending approval cards.
     */
    public function pendingApproval(Request $request): View
    {
        $query = WeddingCard::with(['user', 'designTemplate'])
            ->pendingApproval();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $pendingCards = $query->orderBy('created_at', 'asc')->paginate(15);

        return view('admin.cards.pending', compact('pendingCards'));
    }

    /**
     * Approve a wedding card.
     */
    public function approve(Request $request, WeddingCard $card): JsonResponse
    {
        if (! $card->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'This card is not pending approval.',
            ], 400);
        }

        $card->approve(auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Wedding card approved and published successfully!',
        ]);
    }

    /**
     * Reject a wedding card.
     */
    public function reject(RejectCardRequest $request, WeddingCard $card): JsonResponse
    {
        if (! $card->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'This card is not pending approval.',
            ], 400);
        }

        $card->reject(auth()->id(), $request->reason);

        return response()->json([
            'success' => true,
            'message' => 'Wedding card rejected successfully!',
        ]);
    }
}
