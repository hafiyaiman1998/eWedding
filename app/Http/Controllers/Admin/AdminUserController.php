<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the clients.
     */
    public function index(Request $request): View
    {
        $query = User::clients()->with('weddingCards');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new client.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'type' => 'user', // Always create as user/client
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Client account created successfully.');
    }

    /**
     * Display the specified client.
     */
    public function show(User $user): View
    {
        // Ensure we're only showing clients, not other admins
        if (! $user->isUser()) {
            abort(404);
        }

        $user->load(['weddingCards.designTemplate']);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(User $user): View
    {
        // Ensure we're only editing clients, not other admins
        if (! $user->isUser()) {
            abort(404);
        }

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified client in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        // Ensure we're only updating clients, not other admins
        if (! $user->isUser()) {
            abort(404);
        }

        $validated = $request->validated();

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', 'Client account updated successfully.');
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Request $request, User $user): JsonResponse|RedirectResponse
    {
        // Ensure we're only deleting clients, not other admins
        if (! $user->isUser()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.',
                ], 403);
            }
            abort(404);
        }

        try {
            $userName = $user->name;
            $weddingCardsCount = $user->weddingCards()->count();

            // Delete the user (this will cascade delete wedding cards)
            $user->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Client '{$userName}' and {$weddingCardsCount} wedding cards have been deleted successfully.",
                    'redirect_url' => route('admin.users.index'),
                ]);
            }

            return redirect()->route('admin.users.index')
                ->with('success', "Client '{$userName}' has been deleted successfully.");

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the client. Please try again.',
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'An error occurred while deleting the client.');
        }
    }
}
