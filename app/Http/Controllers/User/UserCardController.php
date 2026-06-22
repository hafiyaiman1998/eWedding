<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWeddingCardRequest;
use App\Http\Requests\UpdateWeddingCardRequest;
use App\Models\DesignTemplate;
use App\Models\WeddingCard;
use App\Services\FileUploadService;
use App\Services\TemplateRenderer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserCardController extends Controller
{
    public function __construct(
        private TemplateRenderer $templateRenderer,
        private FileUploadService $fileUploadService,
    ) {}

    /**
     * Display a listing of user's wedding cards.
     */
    public function index(): View
    {
        $cards = Auth::user()->weddingCards()
            ->with('designTemplate')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('user.cards.index', compact('cards'));
    }

    /**
     * Show the form for creating a new wedding card.
     */
    public function create(): View|RedirectResponse
    {
        // Check if user has reached their card limit
        if (WeddingCard::userHasReachedLimit(Auth::id())) {
            $maxCards = \App\Models\Setting::get('max_cards_per_user', 10);

            return redirect()->route('user.cards.index')
                ->with('error', "You have reached your maximum limit of {$maxCards} wedding cards. Please delete some cards to create new ones.");
        }

        $templates = DesignTemplate::active()->orderBy('name')->get();

        return view('user.cards.create', compact('templates'));
    }

    /**
     * Store a newly created wedding card in storage.
     */
    public function store(StoreWeddingCardRequest $request): RedirectResponse
    {
        // Check if user has reached their card limit
        if (WeddingCard::userHasReachedLimit(Auth::id())) {
            $maxCards = \App\Models\Setting::get('max_cards_per_user', 10);

            return redirect()->route('user.cards.index')
                ->with('error', "You have reached your maximum limit of {$maxCards} wedding cards. Please delete some cards to create new ones.");
        }

        // Start with empty card details
        $cardDetails = $request->card_details ?? [];

        // Process file uploads
        if ($request->hasFile('variable_files')) {
            foreach ($request->file('variable_files') as $fieldName => $file) {
                if ($file && $file->isValid()) {
                    try {
                        // Store the file (type detection + path handled by the service)
                        $filePath = $this->fileUploadService->storeUserCardFile($file, Auth::id());

                        // Add file path to card details
                        $cardDetails[$fieldName] = $filePath;

                        Log::info('File uploaded successfully', [
                            'field' => $fieldName,
                            'path' => $filePath,
                            'original_name' => $file->getClientOriginalName(),
                        ]);
                    } catch (\Exception $e) {
                        Log::error("File upload failed for {$fieldName}: ".$e->getMessage(), [
                            'exception' => $e,
                            'trace' => $e->getTraceAsString(),
                        ]);
                        // Continue processing other files
                    }
                }
            }
        }

        Log::info('Final card details before creation', ['card_details' => $cardDetails]);

        // Get template to merge with default variables if card_details is empty
        $template = \App\Models\DesignTemplate::find($request->design_template_id);
        if ($template && $template->default_variables && empty($cardDetails)) {
            // Only merge default variables that don't have corresponding user input
            foreach ($template->default_variables as $key => $defaultValue) {
                if (! isset($cardDetails[$key]) && ! empty($defaultValue)) {
                    $cardDetails[$key] = $defaultValue;
                }
            }
        }

        // Calculate expiry date based on default settings
        $expiryDays = \App\Models\Setting::get('default_card_expiry_days', 365);
        $expiryDate = now()->addDays($expiryDays);

        // Check auto-approve setting
        $autoApprove = \App\Models\Setting::get('auto_approve_cards', true);
        $approvalStatus = $autoApprove ? 'approved' : 'pending';
        $isPublished = $autoApprove ? false : false; // Will be true when approved
        $approvedAt = $autoApprove ? now() : null;

        Log::info('Attempting to create wedding card', [
            'card_data' => [
                'user_id' => Auth::id(),
                'design_template_id' => $request->design_template_id,
                'title' => $request->title,
                'card_details' => $cardDetails,
                'custom_message' => $request->custom_message,
                'unique_url' => Str::random(12),
                'is_published' => $isPublished,
                'approval_status' => $approvalStatus,
                'approved_at' => $approvedAt,
                'expiry_date' => $expiryDate,
            ],
        ]);

        $card = WeddingCard::create([
            'user_id' => Auth::id(),
            'design_template_id' => $request->design_template_id,
            'title' => $request->title,
            'card_details' => $cardDetails,
            'custom_message' => $request->custom_message,
            'unique_url' => Str::random(12),
            'is_published' => $isPublished,
            'approval_status' => $approvalStatus,
            'approved_at' => $approvedAt,
            'expiry_date' => $expiryDate,
        ]);

        Log::info('Wedding card created successfully', ['card_id' => $card->id]);

        $message = $autoApprove
            ? 'Wedding card created successfully! You can now edit and customize it.'
            : 'Wedding card created successfully! It has been submitted for admin approval. You will be notified once it is reviewed.';

        return redirect()->route('user.cards.edit', $card)
            ->with('success', $message);
    }

    /**
     * Display the specified wedding card.
     */
    public function show(WeddingCard $card): View
    {
        $this->authorize('view', $card);

        $card->load('designTemplate');

        return view('user.cards.show', compact('card'));
    }

    /**
     * Show the form for editing the specified wedding card.
     */
    public function edit(WeddingCard $card): View
    {
        $this->authorize('update', $card);

        $card->load('designTemplate');
        $templates = DesignTemplate::active()->orderBy('name')->get();

        // Check if auto-approve is enabled
        $autoApproveEnabled = \App\Models\Setting::get('auto_approve_cards', true);

        return view('user.cards.edit', compact('card', 'templates', 'autoApproveEnabled'));
    }

    /**
     * Update the specified wedding card in storage.
     */
    public function update(UpdateWeddingCardRequest $request, WeddingCard $card): RedirectResponse
    {
        $this->authorize('update', $card);

        // Start with existing card details
        $cardDetails = $request->card_details ?? [];

        // Process file uploads
        if ($request->hasFile('variable_files')) {
            foreach ($request->file('variable_files') as $fieldName => $file) {
                if ($file && $file->isValid()) {
                    try {
                        // Delete old file if it exists
                        if (isset($card->card_details[$fieldName]) && $card->card_details[$fieldName]) {
                            $this->fileUploadService->deleteByPath($card->card_details[$fieldName]);
                        }

                        // Store the new file (type detection + path handled by the service)
                        $filePath = $this->fileUploadService->storeUserCardFile($file, $card->user_id);

                        // Add file path to card details
                        $cardDetails[$fieldName] = $filePath;

                    } catch (\Exception $e) {
                        Log::error("File upload failed for {$fieldName}: ".$e->getMessage());
                        // Continue processing other files
                    }
                }
            }
        }

        $updateData = [
            'design_template_id' => $request->design_template_id,
            'title' => $request->title,
            'card_details' => $cardDetails,
            'custom_message' => $request->custom_message,
        ];

        // Handle publish action
        if ($request->has('publish')) {
            // Check if auto-approve is enabled
            $autoApprove = \App\Models\Setting::get('auto_approve_cards', true);

            if ($autoApprove) {
                // Auto-approve enabled: directly publish the card
                $updateData['is_published'] = true;
                $updateData['approval_status'] = 'approved';
                $updateData['approved_at'] = now();
                $updateData['approved_by'] = null; // System auto-approval
            } else {
                // Auto-approve disabled: submit for admin approval
                $updateData['approval_status'] = 'pending';
                $updateData['is_published'] = false;
            }
        }

        $card->update($updateData);

        $message = 'Wedding card updated successfully!';
        if ($request->has('publish')) {
            $autoApprove = \App\Models\Setting::get('auto_approve_cards', true);
            if ($autoApprove) {
                $message = 'Wedding card published successfully!';
            } else {
                $message = 'Wedding card submitted for admin approval! You will be notified once it is reviewed.';
            }
        }

        return redirect()->route('user.cards.edit', $card)
            ->with('success', $message);
    }

    /**
     * Remove the specified wedding card from storage.
     */
    public function destroy(Request $request, WeddingCard $card): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $card);

        try {
            $cardTitle = $card->title ?: 'Untitled Card';
            $card->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Wedding card '{$cardTitle}' has been deleted successfully.",
                    'redirect_url' => route('user.cards.index'),
                ]);
            }

            return redirect()->route('user.cards.index')
                ->with('success', 'Wedding card deleted successfully!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the wedding card. Please try again.',
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'An error occurred while deleting the wedding card.');
        }
    }

    /**
     * Preview the wedding card.
     */
    public function preview(WeddingCard $card): View
    {
        $this->authorize('view', $card);

        $card->load('designTemplate');

        return view('user.cards.preview', compact('card'));
    }

    /**
     * Toggle the published status of the wedding card.
     */
    public function togglePublished(WeddingCard $card): RedirectResponse
    {
        $this->authorize('update', $card);

        // If trying to publish
        if (! $card->is_published) {
            // Check if auto-approve is enabled
            $autoApprove = \App\Models\Setting::get('auto_approve_cards', true);

            if ($autoApprove) {
                // Auto-approve enabled: directly publish the card
                $card->update([
                    'is_published' => true,
                    'approval_status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => null, // System auto-approval
                ]);

                return back()->with('success', 'Wedding card published successfully!');
            } else {
                // Auto-approve disabled: submit for admin approval
                $card->update([
                    'approval_status' => 'pending',
                    'is_published' => false,
                ]);

                return back()->with('success', 'Wedding card submitted for admin approval! You will be notified once it is reviewed.');
            }
        } else {
            // If unpublishing, just unpublish it
            $card->update([
                'is_published' => false,
            ]);

            return back()->with('success', 'Wedding card unpublished successfully!');
        }
    }

    /**
     * Show sharing options for the wedding card.
     */
    public function share(WeddingCard $card): View|RedirectResponse
    {
        $this->authorize('view', $card);

        if (! $card->is_published) {
            return redirect()->route('user.cards.edit', $card)
                ->with('error', 'You must publish the card before sharing it.');
        }

        // Load relationships for analytics
        $card->load(['designTemplate', 'analytics', 'rsvps']);

        // Calculate real analytics data for sharing stats
        $totalViews = $card->analytics()->views()->count();
        $uniqueViews = $card->analytics()->views()->distinct('ip_address')->count('ip_address');
        $totalShares = $card->analytics()->shares()->count();
        $totalRsvps = $card->rsvps()->count();

        $analytics = [
            'total_views' => $totalViews,
            'unique_views' => $uniqueViews,
            'total_shares' => $totalShares,
            'total_rsvps' => $totalRsvps,
        ];

        return view('user.sharing.index', compact('card', 'analytics'));
    }

    /**
     * Show analytics for the wedding card.
     */
    public function analytics(WeddingCard $card): View
    {
        $this->authorize('view', $card);

        // Load relationships for analytics
        $card->load(['designTemplate', 'analytics', 'rsvps']);

        // Calculate real analytics data
        $totalViews = $card->analytics()->views()->count();
        $uniqueViews = $card->analytics()->views()->distinct('ip_address')->count('ip_address');
        $totalRsvps = $card->rsvps()->count();
        $attending = $card->rsvps()->attending()->count();
        $notAttending = $card->rsvps()->notAttending()->count();
        $totalGuestsAttending = $card->rsvps()->attending()->sum('number_of_guests');

        // Get recent views (last 7 days)
        $recentViews = collect(range(6, 0))->map(function ($daysAgo) use ($card) {
            $date = now()->subDays($daysAgo);
            $views = $card->analytics()
                ->views()
                ->whereDate('created_at', $date)
                ->count();

            return [
                'date' => $date->format('M d'),
                'views' => $views,
            ];
        });

        $analytics = [
            'total_views' => $totalViews,
            'unique_views' => $uniqueViews,
            'total_rsvps' => $totalRsvps,
            'attending' => $attending,
            'not_attending' => $notAttending,
            'total_guests_attending' => $totalGuestsAttending,
            'recent_views' => $recentViews,
        ];

        return view('user.analytics.index', compact('card', 'analytics'));
    }

    /**
     * Show RSVPs for the wedding card.
     */
    public function rsvps(WeddingCard $card): View
    {
        $this->authorize('view', $card);

        $rsvps = $card->rsvps()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => $card->rsvps()->count(),
            'attending' => $card->rsvps()->attending()->count(),
            'not_attending' => $card->rsvps()->notAttending()->count(),
            'total_guests' => $card->rsvps()->attending()->sum('number_of_guests'),
        ];

        return view('user.rsvps.index', compact('card', 'rsvps', 'stats'));
    }

    /**
     * Get template preview for selection.
     */
    public function templatePreview(DesignTemplate $template): View|Response
    {
        // Use real data from template's default_variables if available
        $previewData = $template->default_variables ?? [];

        // If no default variables exist, provide fallback sample data
        if (empty($previewData)) {
            $previewData = [
                'bride_name' => 'Sarah',
                'groom_name' => 'Ahmad',
                'wedding_date' => '15 Januari 2024',
                'wedding_time' => '10:00 AM',
                'venue_name' => 'Dewan Serbaguna',
                'venue_address' => 'Jalan Raya No. 123, Kuala Lumpur',
                'rsvp_date' => '10 Januari 2024',
                'contact_bride' => '012-3456789',
                'contact_groom' => '013-9876543',
            ];
        }

        // Check if template has full_html_template, if not use blade_template
        $templateContent = $template->full_html_template ?? $template->blade_template;

        if (! empty($templateContent)) {
            return $this->renderTemplatePreview($template, $previewData, $templateContent);
        }

        // Fallback to simple preview if no template content
        return view('user.templates.preview', compact('template', 'previewData'));
    }

    /**
     * Render the template with proper variable processing.
     */
    private function renderTemplatePreview(DesignTemplate $template, array $previewData, string $htmlContent): Response
    {
        // Add gallery photos if not present
        for ($i = 1; $i <= 6; $i++) {
            if (! isset($previewData["gallery_photo_$i"])) {
                $previewData["gallery_photo_$i"] = 'https://images.unsplash.com/photo-'.rand(1500000000000, 1600000000000).'?w=400&h=300&fit=crop';
            }
        }

        $htmlContent = $this->templateRenderer->render($htmlContent, $previewData, [
            'for_callback' => true,
        ]);

        return response($htmlContent)->header('Content-Type', 'text/html');
    }

    /**
     * Get template with its default variables for form population.
     */
    public function getTemplateData(DesignTemplate $template): JsonResponse
    {
        // Extract variables from full_html_template if available, otherwise use default_variables
        $extractedVariables = [];

        if ($template->full_html_template) {
            $extractedVariables = $this->extractVariablesFromTemplate($template->full_html_template);
        }

        // Merge extracted variables with existing default_variables, preferring default_variables for values
        $finalVariables = $extractedVariables;
        if ($template->default_variables) {
            foreach ($template->default_variables as $key => $value) {
                $finalVariables[$key] = $value; // Override with actual default values
            }
        }

        return response()->json([
            'default_variables' => $finalVariables,
            'full_html_template' => $template->full_html_template,
            'name' => $template->name,
            'category' => $template->category,
            'is_malaysian_design' => $template->is_malaysian_design,
            'preview_image' => $template->preview_image,
            'description' => $template->description,
        ]);
    }

    /**
     * Extract variables from template HTML content using PHP regex patterns.
     */
    private function extractVariablesFromTemplate($templateCode): array
    {
        try {
            $variables = [];

            Log::info('=== PHP VARIABLE EXTRACTION DEBUG ===');
            Log::info('Template code length: '.strlen($templateCode));

            // Step 1: Extract all regular $details variables - CORRECTED PATTERN
            // This matches the JavaScript pattern: /\{\{\s*\$details\[\s*["']([^"'$]+)["']\s*\]\s*(?:\?\?\s*[^}]+)?\s*\}\}/g
            preg_match_all('/\{\{\s*\$details\[\s*["\']([^"\'$]+)["\']\s*\]\s*(?:\?\?\s*[^}]+)?\s*\}\}/', $templateCode, $matches1);
            foreach ($matches1[1] as $varName) {
                if (! empty(trim($varName))) {
                    $variables[$varName] = '';
                    Log::info("Pattern 1 found: {$varName}");
                }
            }

            // Step 2: Look for @for loops to find iteration ranges
            preg_match_all('/@for\s*\(\s*\$([a-zA-Z_][a-zA-Z0-9_]*)\s*=\s*(\d+)\s*;\s*\$[a-zA-Z_][a-zA-Z0-9_]*\s*<=?\s*(\d+)/', $templateCode, $forMatches);
            $loopRanges = [];

            for ($i = 0; $i < count($forMatches[0]); $i++) {
                $iteratorVar = $forMatches[1][$i];
                $startNum = (int) $forMatches[2][$i];
                $endNum = (int) $forMatches[3][$i];
                $loopRanges[$iteratorVar] = ['start' => $startNum, 'end' => $endNum];
                Log::info("Found @for loop: \${$iteratorVar} from {$startNum} to {$endNum}");
            }

            // Step 3: Find variables that use loop iterators

            // Pattern A: {{ $details['variable_' . $i] }}
            preg_match_all('/\{\{\s*\$details\[\s*["\']([^"\']+)_["\'\s*\.\s*\$([a-zA-Z_][a-zA-Z0-9_]*)\s*\]/', $templateCode, $concatMatches);
            for ($i = 0; $i < count($concatMatches[0]); $i++) {
                $baseName = $concatMatches[1][$i];
                $iteratorVar = $concatMatches[2][$i];
                $range = $loopRanges[$iteratorVar] ?? ['start' => 1, 'end' => 6];

                Log::info("Pattern A found: {$baseName}_ with iterator \${$iteratorVar}");

                for ($j = $range['start']; $j <= $range['end']; $j++) {
                    $loopVar = "{$baseName}_{$j}";
                    $variables[$loopVar] = '';
                    Log::info("  -> Added: {$loopVar}");
                }
            }

            // Pattern B: $details["variable_$i"] (inside @if conditions)
            preg_match_all('/\$details\[\s*["\']([^"\']+)_\$([a-zA-Z_][a-zA-Z0-9_]*)["\'\s*\]/', $templateCode, $interpMatches);
            for ($i = 0; $i < count($interpMatches[0]); $i++) {
                $baseName = $interpMatches[1][$i];
                $iteratorVar = $interpMatches[2][$i];
                $range = $loopRanges[$iteratorVar] ?? ['start' => 1, 'end' => 6];

                Log::info("Pattern B found: {$baseName}_ with iterator \${$iteratorVar}");

                for ($j = $range['start']; $j <= $range['end']; $j++) {
                    $loopVar = "{$baseName}_{$j}";
                    $variables[$loopVar] = '';
                    Log::info("  -> Added: {$loopVar}");
                }
            }

            // Step 4: Simple Gallery Detection - Just check for gallery_photo + @for
            if (strpos($templateCode, 'gallery_photo') !== false) {
                Log::info('Found gallery_photo in template');

                // Look for @for loop with numbers to get the range
                $startNum = 1;
                $endNum = 6;

                if (preg_match('/@for\s*\(\s*\$\w+\s*=\s*(\d+)\s*;\s*\$\w+\s*<=?\s*(\d+)/', $templateCode, $forMatch)) {
                    $startNum = (int) $forMatch[1];
                    $endNum = (int) $forMatch[2];
                    Log::info("Found @for loop range: {$startNum} to {$endNum}");
                } else {
                    Log::info('No @for loop found, using default range 1 to 6');
                }

                Log::info("Adding gallery_photo_{$startNum} through gallery_photo_{$endNum}");
                for ($i = $startNum; $i <= $endNum; $i++) {
                    $galleryVar = "gallery_photo_{$i}";
                    $variables[$galleryVar] = '';
                    Log::info("  -> FORCED ADD: {$galleryVar}");
                }
            }

            // Step 5: Absolute Fallback - If we see ANY @for loop, add gallery photos
            $existingGalleryVars = array_filter(array_keys($variables), function ($v) {
                return strpos($v, 'gallery_photo_') === 0;
            });

            if (empty($existingGalleryVars)) {
                if (preg_match('/@for\s*\(\s*\$\w+\s*=\s*(\d+)\s*;\s*\$\w+\s*<=?\s*(\d+)/', $templateCode, $anyForLoop)) {
                    $startNum = (int) $anyForLoop[1];
                    $endNum = (int) $anyForLoop[2];
                    Log::info("No gallery photos detected but found @for loop {$startNum} to {$endNum}");
                    Log::info('Adding gallery_photo variables as fallback');

                    for ($i = $startNum; $i <= $endNum; $i++) {
                        $galleryVar = "gallery_photo_{$i}";
                        $variables[$galleryVar] = '';
                        Log::info("  -> FALLBACK ADD: {$galleryVar}");
                    }
                }
            }

            Log::info('=== EXTRACTION COMPLETE ===');
            Log::info('Total variables extracted: '.count($variables));
            Log::info('Variables: '.implode(', ', array_keys($variables)));

            return $variables;

        } catch (\Exception $e) {
            Log::error('Error in extractVariablesFromTemplate: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());

            // Return empty array on error to prevent fatal error
            return [];
        }
    }
}
