<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WeddingCard;
use App\Models\DesignTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserCardController extends Controller
{
    /**
     * Display a listing of user's wedding cards.
     */
    public function index()
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
    public function create()
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
    public function store(Request $request)
    {
        // Check if user has reached their card limit
        if (WeddingCard::userHasReachedLimit(Auth::id())) {
            $maxCards = \App\Models\Setting::get('max_cards_per_user', 10);
            return redirect()->route('user.cards.index')
                ->with('error', "You have reached your maximum limit of {$maxCards} wedding cards. Please delete some cards to create new ones.");
        }

        $request->validate([
            'design_template_id' => 'required|exists:design_templates,id',
            'title' => 'required|string|max:255',
            'card_details' => 'array',
            'custom_message' => 'nullable|string',
            'variable_files.*' => 'nullable|file|max:20000', // 20MB max per file
        ]);

        // Start with empty card details
        $cardDetails = $request->card_details ?? [];

        // Process file uploads
        if ($request->hasFile('variable_files')) {
            foreach ($request->file('variable_files') as $fieldName => $file) {
                if ($file && $file->isValid()) {
                    try {
                        // Determine storage path based on file type and user ID
                        $isVideo = in_array($file->getClientOriginalExtension(), ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm']);
                        $fileType = $isVideo ? 'videos' : 'images';
                        $storagePath = "user-card/" . Auth::id() . "/{$fileType}";
                        
                        // Store the file
                        $filePath = $file->store($storagePath, 'public');
                        
                        // Add file path to card details
                        $cardDetails[$fieldName] = $filePath;
                        
                        Log::info("File uploaded successfully", [
                            'field' => $fieldName,
                            'path' => $filePath,
                            'original_name' => $file->getClientOriginalName()
                        ]);
                    } catch (\Exception $e) {
                        Log::error("File upload failed for {$fieldName}: " . $e->getMessage(), [
                            'exception' => $e,
                            'trace' => $e->getTraceAsString()
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
                if (!isset($cardDetails[$key]) && !empty($defaultValue)) {
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
            ]
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
    public function show(WeddingCard $card)
    {
        $this->authorize('view', $card);
        
        $card->load('designTemplate');
        return view('user.cards.show', compact('card'));
    }

    /**
     * Show the form for editing the specified wedding card.
     */
    public function edit(WeddingCard $card)
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
    public function update(Request $request, WeddingCard $card)
    {
        $this->authorize('update', $card);

        $request->validate([
            'design_template_id' => 'required|exists:design_templates,id',
            'title' => 'required|string|max:255',
            'card_details' => 'array',
            'custom_message' => 'nullable|string',
            'variable_files.*' => 'nullable|file|max:20000', // 20MB max per file
        ]);

        // Start with existing card details
        $cardDetails = $request->card_details ?? [];

        // Process file uploads
        if ($request->hasFile('variable_files')) {
            foreach ($request->file('variable_files') as $fieldName => $file) {
                if ($file && $file->isValid()) {
                    try {
                        // Delete old file if it exists
                        if (isset($card->card_details[$fieldName]) && $card->card_details[$fieldName]) {
                            $oldFilePath = $card->card_details[$fieldName];
                            if (Storage::disk('public')->exists($oldFilePath)) {
                                Storage::disk('public')->delete($oldFilePath);
                            }
                        }

                        // Determine storage path based on file type and user ID
                        $isVideo = in_array($file->getClientOriginalExtension(), ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm']);
                        $fileType = $isVideo ? 'videos' : 'images';
                        $storagePath = "user-card/" . $card->user_id . "/{$fileType}";
                        
                        // Store the new file
                        $filePath = $file->store($storagePath, 'public');
                        
                        // Add file path to card details
                        $cardDetails[$fieldName] = $filePath;
                        
                    } catch (\Exception $e) {
                        Log::error("File upload failed for {$fieldName}: " . $e->getMessage());
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
    public function destroy(Request $request, WeddingCard $card)
    {
        $this->authorize('delete', $card);
        
        try {
            $cardTitle = $card->title ?: 'Untitled Card';
            $card->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Wedding card '{$cardTitle}' has been deleted successfully.",
                    'redirect_url' => route('user.cards.index')
                ]);
            }

            return redirect()->route('user.cards.index')
                ->with('success', 'Wedding card deleted successfully!');
                
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the wedding card. Please try again.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'An error occurred while deleting the wedding card.');
        }
    }

    /**
     * Preview the wedding card.
     */
    public function preview(WeddingCard $card)
    {
        $this->authorize('view', $card);
        
        $card->load('designTemplate');
        return view('user.cards.preview', compact('card'));
    }

    /**
     * Toggle the published status of the wedding card.
     */
    public function togglePublished(WeddingCard $card)
    {
        $this->authorize('update', $card);

        // If trying to publish
        if (!$card->is_published) {
            // Check if auto-approve is enabled
            $autoApprove = \App\Models\Setting::get('auto_approve_cards', true);
            
            if ($autoApprove) {
                // Auto-approve enabled: directly publish the card
                $card->update([
                    'is_published' => true,
                    'approval_status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => null // System auto-approval
                ]);
                
                return back()->with('success', 'Wedding card published successfully!');
            } else {
                // Auto-approve disabled: submit for admin approval
                $card->update([
                    'approval_status' => 'pending',
                    'is_published' => false
                ]);
                
                return back()->with('success', 'Wedding card submitted for admin approval! You will be notified once it is reviewed.');
            }
        } else {
            // If unpublishing, just unpublish it
            $card->update([
                'is_published' => false
            ]);
            
            return back()->with('success', 'Wedding card unpublished successfully!');
        }
    }

    /**
     * Show sharing options for the wedding card.
     */
    public function share(WeddingCard $card)
    {
        $this->authorize('view', $card);
        
        if (!$card->is_published) {
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
    public function analytics(WeddingCard $card)
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
                'views' => $views
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
    public function rsvps(WeddingCard $card)
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
    public function templatePreview(DesignTemplate $template)
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
        
        if (!empty($templateContent)) {
            return $this->renderTemplatePreview($template, $previewData, $templateContent);
        }

        // Fallback to simple preview if no template content
        return view('user.templates.preview', compact('template', 'previewData'));
    }

    /**
     * Render the template with proper variable processing.
     */
    private function renderTemplatePreview(DesignTemplate $template, array $previewData, string $htmlContent)
    {
        // Add gallery photos if not present
        for ($i = 1; $i <= 6; $i++) {
            if (!isset($previewData["gallery_photo_$i"])) {
                $previewData["gallery_photo_$i"] = 'https://images.unsplash.com/photo-' . rand(1500000000000, 1600000000000) . '?w=400&h=300&fit=crop';
            }
        }

        // Process @for LOOPS first (gallery sections)
        if (strpos($htmlContent, '@for') !== false) {
            $forPos = strpos($htmlContent, '@for');
            $endforPos = strpos($htmlContent, '@endfor', $forPos);
            
            if ($endforPos !== false) {
                $forSection = substr($htmlContent, $forPos, $endforPos - $forPos + 7);
                
                // Parse the @for loop to extract parameters
                if (preg_match('/@for\s*\(\s*\$(\w+)\s*=\s*(\d+);\s*\$\w+\s*<=\s*(\d+);\s*\$\w+\+\+\s*\)/', $forSection, $matches)) {
                    $startValue = (int)$matches[2];
                    $endValue = (int)$matches[3];
                    
                    // Generate gallery content
                    $galleryContent = '';
                    $galleryPhotos = [
                        'https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=400&h=300&fit=crop&crop=center',
                        'https://images.unsplash.com/photo-1465495976277-4387d4b0e4a6?w=400&h=300&fit=crop&crop=center',
                        'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=400&h=300&fit=crop&crop=center',
                        'https://images.unsplash.com/photo-1606216794074-735e91aa2c92?w=400&h=300&fit=crop&crop=center',
                        'https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=400&h=300&fit=crop&crop=center',
                        'https://images.unsplash.com/photo-1520854221256-17451cc331bf?w=400&h=300&fit=crop&crop=center',
                    ];
                    
                    for ($i = $startValue; $i <= $endValue; $i++) {
                        $photoIndex = ($i - $startValue) % count($galleryPhotos);
                        $photoUrl = $previewData["gallery_photo_$i"] ?? $galleryPhotos[$photoIndex];
                        $galleryContent .= '                <div class="gallery-item fade-in">' . "\n";
                        $galleryContent .= '                    <img src="' . $photoUrl . '" alt="Gallery Photo ' . $i . '">' . "\n";
                        $galleryContent .= '                </div>' . "\n";
                    }
                    
                    $htmlContent = str_replace($forSection, $galleryContent, $htmlContent);
                }
            }
        }

        // Process all Blade template variables - COMPREHENSIVE VERSION like AdminTemplateController
        
        // Handle PHP functions like strtoupper() with Blade variables
        $htmlContent = preg_replace_callback(
            '/\{\{\s*strtoupper\(\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\)\s*\}\}/',
            function($matches) use ($previewData) {
                $key = $matches[1];
                $fallback = $matches[2];
                return strtoupper($previewData[$key] ?? $fallback);
            },
            $htmlContent
        );
        
        // Handle date() function
        $htmlContent = preg_replace_callback(
            '/\{\{\s*date\("([^"]+)"\)\s*\}\}/',
            function($matches) {
                $format = $matches[1];
                return date($format);
            },
            $htmlContent
        );
        
        // Handle substr() functions with Blade variables
        $htmlContent = preg_replace_callback(
            '/\{\{\s*substr\(\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*,\s*(\d+)\s*,\s*(\d+)\)\s*\}\}/',
            function($matches) use ($previewData) {
                $key = $matches[1];
                $fallback = $matches[2];
                $start = (int)$matches[3];
                $length = (int)$matches[4];
                $value = $previewData[$key] ?? $fallback;
                return substr($value, $start, $length);
            },
            $htmlContent
        );
        
        // Handle complex Blade variables with multiple fallbacks (mixed quotes)
        $htmlContent = preg_replace_callback(
            '/\{\{\s*\$details\[\'([^\']+)\'\]\s*\?\?\s*\$details\[\'([^\']+)\'\]\s*\?\?\s*\'([^\']*)\'\s*\}\}/',
            function($matches) use ($previewData) {
                $key1 = $matches[1];
                $key2 = $matches[2];
                $fallback = $matches[3];
                return $previewData[$key1] ?? $previewData[$key2] ?? $fallback;
            },
            $htmlContent
        );
        
        // Handle complex Blade variables with multiple fallbacks (double quotes)
        $htmlContent = preg_replace_callback(
            '/\{\{\s*\$details\["([^"]+)"\]\s*\?\?\s*\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*\}\}/',
            function($matches) use ($previewData) {
                $key1 = $matches[1];
                $key2 = $matches[2];
                $fallback = $matches[3];
                return $previewData[$key1] ?? $previewData[$key2] ?? $fallback;
            },
            $htmlContent
        );
        
        // Handle Blade variables with null coalescing operator (single quotes)
        $htmlContent = preg_replace_callback(
            '/\{\{\s*\$details\[\'([^\']+)\'\]\s*\?\?\s*\'([^\']*)\'\s*\}\}/',
            function($matches) use ($previewData) {
                $key = $matches[1];
                $fallback = $matches[2];
                return $previewData[$key] ?? $fallback;
            },
            $htmlContent
        );
        
        // Handle Blade variables with null coalescing operator (double quotes)
        $htmlContent = preg_replace_callback(
            '/\{\{\s*\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*\}\}/',
            function($matches) use ($previewData) {
                $key = $matches[1];
                $fallback = $matches[2];
                return $previewData[$key] ?? $fallback;
            },
            $htmlContent
        );
        
        // Handle simple Blade variables without fallback (single quotes)
        $htmlContent = preg_replace_callback(
            '/\{\{\s*\$details\[\'([^\']+)\'\]\s*\}\}/',
            function($matches) use ($previewData) {
                $key = $matches[1];
                return $previewData[$key] ?? '';
            },
            $htmlContent
        );
        
        // Handle simple Blade variables without fallback (double quotes)
        $htmlContent = preg_replace_callback(
            '/\{\{\s*\$details\["([^"]+)"\]\s*\}\}/',
            function($matches) use ($previewData) {
                $key = $matches[1];
                return $previewData[$key] ?? '';
            },
            $htmlContent
        );

        // Process @if/@else/@endif blocks FIRST before simple @if blocks
        // This prevents the simple @if processing from breaking the @if/@else structure
        
        // Handle @if/@else/@endif statements (double quotes)
        $htmlContent = preg_replace_callback(
            '/@if\(\$details\["([^"]+)"\]\s*\?\?\s*false\)(.*?)@else(.*?)@endif/s',
            function($matches) use ($previewData) {
                $key = $matches[1];
                $ifContent = $matches[2];
                $elseContent = $matches[3];
                return !empty($previewData[$key]) ? $ifContent : $elseContent;
            },
            $htmlContent
        );
        
        // Handle @if/@else/@endif statements (single quotes)
        $htmlContent = preg_replace_callback(
            '/@if\(\$details\[\'([^\']+)\'\]\s*\?\?\s*false\)(.*?)@else(.*?)@endif/s',
            function($matches) use ($previewData) {
                $key = $matches[1];
                $ifContent = $matches[2];
                $elseContent = $matches[3];
                return !empty($previewData[$key]) ? $ifContent : $elseContent;
            },
            $htmlContent
        );
        
        // Handle @if/@else/@endif statements with isset() (double quotes)
        $htmlContent = preg_replace_callback(
            '/@if\(isset\(\$details\["([^"]+)"\]\)\)(.*?)@else(.*?)@endif/s',
            function($matches) use ($previewData) {
                $key = $matches[1];
                $ifContent = $matches[2];
                $elseContent = $matches[3];
                return (isset($previewData[$key]) && !empty($previewData[$key])) ? $ifContent : $elseContent;
            },
            $htmlContent
        );
        
        // Handle @if/@else/@endif statements with isset() (single quotes)
        $htmlContent = preg_replace_callback(
            '/@if\(isset\(\$details\[\'([^\']+)\'\]\)\)(.*?)@else(.*?)@endif/s',
            function($matches) use ($previewData) {
                $key = $matches[1];
                $ifContent = $matches[2];
                $elseContent = $matches[3];
                return (isset($previewData[$key]) && !empty($previewData[$key])) ? $ifContent : $elseContent;
            },
            $htmlContent
        );
        
        // Handle simple @if/@else/@endif statements (double quotes)
        $htmlContent = preg_replace_callback(
            '/@if\(\$details\["([^"]+)"\]\)(.*?)@else(.*?)@endif/s',
            function($matches) use ($previewData) {
                $key = $matches[1];
                $ifContent = $matches[2];
                $elseContent = $matches[3];
                return !empty($previewData[$key]) ? $ifContent : $elseContent;
            },
            $htmlContent
        );
        
        // Handle simple @if/@else/@endif statements (single quotes)
        $htmlContent = preg_replace_callback(
            '/@if\(\$details\[\'([^\']+)\'\]\)(.*?)@else(.*?)@endif/s',
            function($matches) use ($previewData) {
                $key = $matches[1];
                $ifContent = $matches[2];
                $elseContent = $matches[3];
                return !empty($previewData[$key]) ? $ifContent : $elseContent;
            },
            $htmlContent
        );
        
        // After processing @if/@else/@endif blocks, handle remaining simple @if blocks
        
        // Handle @if statements with OR conditions (double quotes)
        $htmlContent = preg_replace_callback(
            '/@if\(\$details\["([^"]+)"\]\s*\|\|\s*\$details\["([^"]+)"\]\)(.*?)@endif/s',
            function($matches) use ($previewData) {
                $key1 = $matches[1];
                $key2 = $matches[2];
                $content = $matches[3];
                return (!empty($previewData[$key1]) || !empty($previewData[$key2])) ? $content : '';
            },
            $htmlContent
        );
        
        // Handle conditional @if statements with ?? false (double quotes)
        $htmlContent = preg_replace_callback(
            '/@if\(\$details\["([^"]+)"\]\s*\?\?\s*false\)(.*?)@endif/s',
            function($matches) use ($previewData) {
                $key = $matches[1];
                $content = $matches[2];
                return !empty($previewData[$key]) ? $content : '';
            },
            $htmlContent
        );
        
        // Handle conditional @if statements with ?? false (single quotes)
        $htmlContent = preg_replace_callback(
            '/@if\(\$details\[\'([^\']+)\'\]\s*\?\?\s*false\)(.*?)@endif/s',
            function($matches) use ($previewData) {
                $key = $matches[1];
                $content = $matches[2];
                return !empty($previewData[$key]) ? $content : '';
            },
            $htmlContent
        );
        
        // Handle @if(isset()) statements (double quotes)
        $htmlContent = preg_replace_callback(
            '/@if\(isset\(\$details\["([^"]+)"\]\)\)(.*?)@endif/s',
            function($matches) use ($previewData) {
                $key = $matches[1];
                $content = $matches[2];
                return isset($previewData[$key]) && !empty($previewData[$key]) ? $content : '';
            },
            $htmlContent
        );
        
        // Handle @if(isset()) statements (single quotes)
        $htmlContent = preg_replace_callback(
            '/@if\(isset\(\$details\[\'([^\']+)\'\]\)\)(.*?)@endif/s',
            function($matches) use ($previewData) {
                $key = $matches[1];
                $content = $matches[2];
                return isset($previewData[$key]) && !empty($previewData[$key]) ? $content : '';
            },
            $htmlContent
        );
        
        // Handle simple @if statements (double quotes)
        $htmlContent = preg_replace_callback(
            '/@if\(\$details\["([^"]+)"\]\)(.*?)@endif/s',
            function($matches) use ($previewData) {
                $key = $matches[1];
                $content = $matches[2];
                return !empty($previewData[$key]) ? $content : '';
            },
            $htmlContent
        );
        
        // Handle simple @if statements (single quotes)
        $htmlContent = preg_replace_callback(
            '/@if\(\$details\[\'([^\']+)\'\]\)(.*?)@endif/s',
            function($matches) use ($previewData) {
                $key = $matches[1];
                $content = $matches[2];
                return !empty($previewData[$key]) ? $content : '';
            },
            $htmlContent
        );
        
        // Clean up any remaining orphaned @endif statements
        $htmlContent = str_replace('@endif', '', $htmlContent);
        
        // Clean up any remaining orphaned @if statements
        $htmlContent = preg_replace('/@if\([^)]+\)/', '', $htmlContent);
        
        // Clean up any remaining orphaned @else statements
        $htmlContent = str_replace('@else', '', $htmlContent);
        
        // Handle @for loops for gallery items
        $htmlContent = preg_replace_callback(
            '/@for\(\$i\s*=\s*(\d+);\s*\$i\s*<=\s*(\d+);\s*\$i\+\+\)(.*?)@endfor/s',
            function($matches) use ($previewData) {
                $start = (int)$matches[1];
                $end = (int)$matches[2];
                $content = $matches[3];
                $result = '';
                
                for ($i = $start; $i <= $end; $i++) {
                    $iterationContent = $content;
                    $iterationContent = str_replace('$i', $i, $iterationContent);
                    
                    // Handle gallery photo variables
                    $iterationContent = preg_replace_callback(
                        '/\{\{\s*\$details\["gallery_photo_"\s*\.\s*\$i\]\s*\?\?\s*false\s*\}\}/',
                        function($m) use ($previewData, $i) {
                            return $previewData["gallery_photo_$i"] ?? false;
                        },
                        $iterationContent
                    );
                    
                    $result .= $iterationContent;
                }
                
                return $result;
            },
            $htmlContent
        );
        
        // Handle any remaining {{ date('Y') }} expressions without quotes  
        $htmlContent = preg_replace_callback(
            '/\{\{\s*date\(\'([^\']+)\'\)\s*\}\}/',
            function($matches) {
                $format = $matches[1];
                return date($format);
            },
            $htmlContent
        );
        
        // Handle complex expressions like {{ date('Y') }} {{ $details["groom_name"] }}
        $htmlContent = preg_replace_callback(
            '/\{\{\s*date\(\'([^\']+)\'\)\s*\}\}\s*\{\{\s*\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*\}\}/',
            function($matches) use ($previewData) {
                $dateFormat = $matches[1];
                $key = $matches[2];
                $fallback = $matches[3];
                return date($dateFormat) . ' ' . ($previewData[$key] ?? $fallback);
            },
            $htmlContent
        );
        
        // Clean up any remaining Blade syntax that might cause issues
        $htmlContent = str_replace('<?php', '', $htmlContent);
        $htmlContent = str_replace('?>', '', $htmlContent);
        
        // Remove any leftover curly braces that might be malformed
        $htmlContent = preg_replace('/\{\{\s*\}\}/', '', $htmlContent);

        return response($htmlContent)->header('Content-Type', 'text/html');
    }

    /**
     * Get template with its default variables for form population.
     */
    public function getTemplateData(DesignTemplate $template)
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
    private function extractVariablesFromTemplate($templateCode)
    {
        try {
            $variables = [];
            
            Log::info('=== PHP VARIABLE EXTRACTION DEBUG ===');
            Log::info('Template code length: ' . strlen($templateCode));
            
            // Step 1: Extract all regular $details variables - CORRECTED PATTERN
            // This matches the JavaScript pattern: /\{\{\s*\$details\[\s*["']([^"'$]+)["']\s*\]\s*(?:\?\?\s*[^}]+)?\s*\}\}/g
            preg_match_all('/\{\{\s*\$details\[\s*["\']([^"\'$]+)["\']\s*\]\s*(?:\?\?\s*[^}]+)?\s*\}\}/', $templateCode, $matches1);
            foreach ($matches1[1] as $varName) {
                if (!empty(trim($varName))) {
                    $variables[$varName] = '';
                    Log::info("Pattern 1 found: {$varName}");
                }
            }
            
            // Step 2: Look for @for loops to find iteration ranges
            preg_match_all('/@for\s*\(\s*\$([a-zA-Z_][a-zA-Z0-9_]*)\s*=\s*(\d+)\s*;\s*\$[a-zA-Z_][a-zA-Z0-9_]*\s*<=?\s*(\d+)/', $templateCode, $forMatches);
            $loopRanges = [];
            
            for ($i = 0; $i < count($forMatches[0]); $i++) {
                $iteratorVar = $forMatches[1][$i];
                $startNum = (int)$forMatches[2][$i];
                $endNum = (int)$forMatches[3][$i];
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
                    $startNum = (int)$forMatch[1];
                    $endNum = (int)$forMatch[2];
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
            $existingGalleryVars = array_filter(array_keys($variables), function($v) {
                return strpos($v, 'gallery_photo_') === 0;
            });
            
            if (empty($existingGalleryVars)) {
                if (preg_match('/@for\s*\(\s*\$\w+\s*=\s*(\d+)\s*;\s*\$\w+\s*<=?\s*(\d+)/', $templateCode, $anyForLoop)) {
                    $startNum = (int)$anyForLoop[1];
                    $endNum = (int)$anyForLoop[2];
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
            Log::info('Total variables extracted: ' . count($variables));
            Log::info('Variables: ' . implode(', ', array_keys($variables)));
            
            return $variables;
            
        } catch (\Exception $e) {
            Log::error('Error in extractVariablesFromTemplate: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return empty array on error to prevent fatal error
            return [];
        }
    }
} 