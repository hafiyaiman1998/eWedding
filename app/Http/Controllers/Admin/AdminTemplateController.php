<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesignTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdminTemplateController extends Controller
{
    /**
     * Display a listing of the design templates.
     */
    public function index(Request $request)
    {
        $query = DesignTemplate::withCount('weddingCards');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        // Filter by Malaysian designs
        if ($request->filled('malaysian') && $request->get('malaysian') == '1') {
            $query->malaysian();
        }

        $templates = $query->latest()->paginate(12);
        $categories = DesignTemplate::distinct('category')->pluck('category');

        return view('admin.templates.index', compact('templates', 'categories'));
    }

    /**
     * Display Malaysian design templates specifically.
     */
    public function malaysian()
    {
        $templates = DesignTemplate::malaysian()
            ->withCount('weddingCards')
            ->latest()
            ->paginate(12);

        return view('admin.templates.malaysian', compact('templates'));
    }

    /**
     * Show the form for creating a new design template.
     */
    public function create()
    {
        return view('admin.templates.create');
    }

    /**
     * Store a newly created design template in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'blade_template' => 'required|string',
            'full_html_template' => 'nullable|string',
            'category' => 'required|string|max:255',
            'is_malaysian_design' => 'boolean',
            'preview_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'default_variables' => 'nullable|json',
            'parse_variables_used' => 'nullable|boolean',
        ]);

        $templateData = $validated;
        $templateData['is_malaysian_design'] = $request->boolean('is_malaysian_design');

        // Handle preview image upload
        if ($request->hasFile('preview_image')) {
            $path = $request->file('preview_image')->store('template-previews', 'public');
            $templateData['preview_image'] = $path;
        }

        // Parse default variables
        if (!empty($validated['default_variables'])) {
            $templateData['default_variables'] = json_decode($validated['default_variables'], true);
        }

        DesignTemplate::create($templateData);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Design template created successfully.');
    }

    /**
     * Display the specified design template.
     */
    public function show(DesignTemplate $template)
    {
        $template->load(['weddingCards.user']);
        return view('admin.templates.show', compact('template'));
    }

    /**
     * Show the form for editing the specified design template.
     */
    public function edit(DesignTemplate $template)
    {
        return view('admin.templates.edit', compact('template'));
    }

    /**
     * Update the specified design template in storage.
     */
    public function update(Request $request, DesignTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'blade_template' => 'required|string',
            'full_html_template' => 'nullable|string',
            'category' => 'required|string|max:255',
            'is_malaysian_design' => 'boolean',
            'preview_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'default_variables' => 'nullable|json',
            'is_active' => 'boolean',
            'parse_variables_used' => 'nullable|boolean',
        ]);

        $templateData = $validated;
        $templateData['is_malaysian_design'] = $request->boolean('is_malaysian_design');
        $templateData['is_active'] = $request->boolean('is_active');

        // Handle preview image upload
        if ($request->hasFile('preview_image')) {
            // Delete old image if exists
            if ($template->preview_image) {
                Storage::disk('public')->delete($template->preview_image);
            }
            $path = $request->file('preview_image')->store('template-previews', 'public');
            $templateData['preview_image'] = $path;
        }

        // Handle default variables and material cleanup
        $oldVariables = $template->default_variables ?? [];
        $newVariables = [];
        if (!empty($validated['default_variables'])) {
            $newVariables = json_decode($validated['default_variables'], true);
        }

        // Check if parse variables was used for more aggressive cleanup
        $parseVariablesUsed = $request->boolean('parse_variables_used');

        // Clean up unused materials
        $this->cleanupUnusedMaterials($template, $oldVariables, $newVariables, $parseVariablesUsed);

        // Parse default variables
        if (!empty($validated['default_variables'])) {
            $templateData['default_variables'] = $newVariables;
        }

        $template->update($templateData);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Design template updated successfully.');
    }

    /**
     * Clean up unused materials when variables are updated.
     */
    private function cleanupUnusedMaterials(DesignTemplate $template, array $oldVariables, array $newVariables, bool $parseVariablesUsed = false)
    {
        // Find old file URLs that are no longer used
        $oldFileUrls = $this->extractFileUrls($oldVariables);
        $newFileUrls = $this->extractFileUrls($newVariables);
        
        // Files to delete are in old but not in new
        $filesToDelete = array_diff($oldFileUrls, $newFileUrls);
        
        // If parse variables was used, also clean up any orphaned template-variables files
        if ($parseVariablesUsed) {
            $this->cleanupOrphanedTemplateFiles($template, $newFileUrls);
        }
        
        foreach ($filesToDelete as $fileUrl) {
            $this->deleteFileFromUrl($fileUrl);
        }
    }

    /**
     * Clean up orphaned template variable files that are no longer referenced.
     */
    private function cleanupOrphanedTemplateFiles(DesignTemplate $template, array $currentFileUrls)
    {
        // Get all files in the template-variables directory
        $allTemplateFiles = Storage::disk('public')->allFiles('template-variables');
        
        foreach ($allTemplateFiles as $filePath) {
            $fileUrl = Storage::url($filePath);
            
            // If this file is not in the current variables list, it's orphaned
            if (!in_array($fileUrl, $currentFileUrls)) {
                // Additional check to see if this file might belong to this template
                // (by checking if filename contains any variable names that might relate to this template)
                $fileName = basename($filePath);
                
                // Check if it's a template-var file (our naming convention)
                if (strpos($fileName, 'template-var-') === 0) {
                    // Delete orphaned template variable files
                    Storage::disk('public')->delete($filePath);
                }
            }
        }
    }

    /**
     * Extract file URLs from variables array.
     */
    private function extractFileUrls(array $variables): array
    {
        $fileUrls = [];
        
        foreach ($variables as $key => $value) {
            // Check if it's a file URL (contains storage path or external URL)
            if (is_string($value) && $this->isFileUrl($value)) {
                $fileUrls[] = $value;
            }
        }
        
        return $fileUrls;
    }

    /**
     * Check if a URL is a file URL (image, video, or audio).
     */
    private function isFileUrl(string $url): bool
    {
        // Check if it's a storage URL or contains file extensions
        return (
            strpos($url, '/storage/') !== false ||
            preg_match('/\.(jpg|jpeg|png|gif|mp4|mov|avi|wmv|flv|webm|mp3|wav|ogg|aac|m4a)$/i', $url)
        ) && !empty(trim($url));
    }

    /**
     * Delete a file from URL.
     */
    private function deleteFileFromUrl(string $url): bool
    {
        // Only delete files from our storage
        if (strpos($url, '/storage/') !== false) {
            // Extract the file path from the storage URL
            $path = str_replace('/storage/', '', parse_url($url, PHP_URL_PATH));
            
            // Check if file exists and delete it
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->delete($path);
            }
        }
        return false;
    }

    /**
     * Remove the specified design template from storage.
     */
    public function destroy(Request $request, DesignTemplate $template)
    {
        try {
            // Check if template is being used
            $weddingCardsCount = $template->weddingCards()->count();
            if ($weddingCardsCount > 0) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot delete template as it is being used by {$weddingCardsCount} wedding cards."
                    ], 422);
                }
                
                return redirect()->route('admin.templates.index')
                    ->with('error', 'Cannot delete template as it is being used by wedding cards.');
            }

            // Delete preview image if exists
            if ($template->preview_image) {
                Storage::disk('public')->delete($template->preview_image);
            }

            $templateName = $template->name;
            $template->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Template '{$templateName}' has been deleted successfully.",
                    'redirect_url' => route('admin.templates.index')
                ]);
            }

            return redirect()->route('admin.templates.index')
                ->with('success', "Template '{$templateName}' has been deleted successfully.");
                
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the template. Please try again.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'An error occurred while deleting the template.');
        }
    }

    /**
     * Preview the template with real default variables data.
     */
    public function preview(DesignTemplate $template)
    {
        // Use real data from template's default_variables
        $previewData = $template->default_variables ?? [];
        
        // If no default variables exist, provide minimal fallback
        if (empty($previewData)) {
            $previewData = [
                'bride_name' => 'Bride Name',
                'groom_name' => 'Groom Name',
                'wedding_date' => date('j F Y'),
                'venue' => 'Wedding Venue'
            ];
        }

        return view('admin.templates.preview', compact('template', 'previewData'));
    }

    /**
     * Show the full HTML template with animations in a new tab.
     */
    public function fullPreview(DesignTemplate $template)
    {
        // EMERGENCY TEST: Write to a file to see if this method is even called
        file_put_contents(storage_path('logs/debug_test.txt'), "fullPreview method called at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        
        // Check if full HTML template exists, otherwise use blade template
        if (empty($template->full_html_template)) {
            // Fallback to blade_template if full_html_template is not available
            $htmlContent = $template->blade_template;
            if (empty($htmlContent)) {
                return response()->json(['error' => 'No template content available.'], 404);
            }
        } else {
            $htmlContent = $template->full_html_template;
        }
        
        file_put_contents(storage_path('logs/debug_test.txt'), "Using " . (empty($template->full_html_template) ? 'blade_template' : 'full_html_template') . "\n", FILE_APPEND);
        
        // Use real data from template's default_variables
        $previewData = $template->default_variables ?? [];
        
        // ALWAYS add gallery photos (even if previewData exists)
        for ($i = 1; $i <= 6; $i++) {
            if (!isset($previewData["gallery_photo_$i"])) {
                $previewData["gallery_photo_$i"] = 'https://images.unsplash.com/photo-' . rand(1500000000000, 1600000000000) . '?w=400&h=300&fit=crop';
            }
        }
        
        // If no default variables exist, provide comprehensive fallback data
        if (empty($template->default_variables)) {
            $previewData = [
                'bride_name' => 'Fatimah',
                'groom_name' => 'Ahmad',
                'bride_full_name' => 'Fatimah Binti Abdullah',
                'groom_full_name' => 'Ahmad Bin Ibrahim',
                'wedding_date' => date('j F Y'),
                'venue' => 'Dewan Serbaguna Komuniti',
                'address' => 'Jalan Taman Melati, 53100 Kuala Lumpur',
                'bride_photo' => 'https://images.unsplash.com/photo-1494790108755-2616c9c0b33d?w=300&h=300&fit=crop&crop=face',
                'groom_photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=300&fit=crop&crop=face',
                'akad_date' => 'Friday, ' . date('j F Y', strtotime('+1 day')),
                'akad_time' => '10:00 AM',
                'reception_date' => 'Saturday, ' . date('j F Y', strtotime('+2 days')),
                'reception_time' => '12:00 PM - 5:00 PM',
                'reception_title' => 'Majlis Bersanding & Reception',
                'reception_description' => 'The grand celebration featuring the traditional Malaysian wedding throne ceremony, followed by a feast and entertainment.',
                'groom_reception_date' => 'Sunday, ' . date('j F Y', strtotime('+3 days')),
                'groom_reception_time' => '11:00 AM - 3:00 PM',
                'groom_reception_title' => 'Majlis Bertandang (Groom\'s Reception)',
                'groom_reception_description' => 'A second reception hosted by the groom\'s family, featuring traditional Malaysian cuisine and customs.',
                'groom_reception_venue' => 'Dewan Komuniti Taman Sari',
                'venue_description' => 'The wedding reception will be held at this beautiful venue with ample parking and easy access.',
                'footer_message' => 'Thank you for celebrating our special day with us!',
                'bride_father' => 'Abdullah Bin Ibrahim',
                'bride_mother' => 'Zainab Binti Ali',
                'groom_father' => 'Mohammad Bin Hassan',
                'groom_mother' => 'Khadijah Binti Ahmad'
            ];
            
            // Add gallery photos
            for ($i = 1; $i <= 6; $i++) {
                $previewData["gallery_photo_$i"] = 'https://images.unsplash.com/photo-' . rand(1500000000000, 1600000000000) . '?w=400&h=300&fit=crop';
            }
        }

        // PROCESS @for LOOPS FIRST - RIGHT AFTER GETTING HTML CONTENT
        // Check if @for exists in the content
        $hasFor = strpos($htmlContent, '@for') !== false;
        file_put_contents(storage_path('logs/debug_test.txt'), "Has @for in content: " . ($hasFor ? 'YES' : 'NO') . "\n", FILE_APPEND);
        
        if ($hasFor) {
            file_put_contents(storage_path('logs/debug_test.txt'), "PROCESSING @for loop IMMEDIATELY\n", FILE_APPEND);
            
            // Find the @for section and log it
            $forPos = strpos($htmlContent, '@for');
            $endforPos = strpos($htmlContent, '@endfor', $forPos);
                         if ($endforPos !== false) {
                 $forSection = substr($htmlContent, $forPos, $endforPos - $forPos + 7);
                 file_put_contents(storage_path('logs/debug_test.txt'), "Found @for section: " . $forSection . "\n", FILE_APPEND);
                 file_put_contents(storage_path('logs/debug_test.txt'), "For section length: " . strlen($forSection) . "\n", FILE_APPEND);
                 
                 // DYNAMIC: Parse the @for loop to extract variable name, start, and end values
                 if (preg_match('/@for\s*\(\s*\$(\w+)\s*=\s*(\d+);\s*\$\w+\s*<=\s*(\d+);\s*\$\w+\+\+\s*\)/', $forSection, $matches)) {
                     $varName = $matches[1];        // e.g., "i" or "j"
                     $startValue = (int)$matches[2]; // e.g., 1
                     $endValue = (int)$matches[3];   // e.g., 6, 8, 4, etc.
                     
                     file_put_contents(storage_path('logs/debug_test.txt'), "Parsed loop: \${$varName} from {$startValue} to {$endValue}\n", FILE_APPEND);
                     
                     // Generate gallery items based on the actual loop parameters
                     $galleryContent = '';
                     $galleryPhotos = [
                         'https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=400&h=300&fit=crop&crop=center', // Wedding couple
                         'https://images.unsplash.com/photo-1465495976277-4387d4b0e4a6?w=400&h=300&fit=crop&crop=center', // Wedding flowers
                         'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=400&h=300&fit=crop&crop=center', // Wedding rings
                         'https://images.unsplash.com/photo-1606216794074-735e91aa2c92?w=400&h=300&fit=crop&crop=center', // Wedding venue
                         'https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=400&h=300&fit=crop&crop=center', // Wedding decoration
                         'https://images.unsplash.com/photo-1520854221256-17451cc331bf?w=400&h=300&fit=crop&crop=center', // Wedding cake
                         'https://images.unsplash.com/photo-1594736797933-d0f29c4d3d28?w=400&h=300&fit=crop&crop=center', // Wedding ceremony
                         'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=400&h=300&fit=crop&crop=center', // Wedding reception
                         'https://images.unsplash.com/photo-1469371670807-013ccf25f16a?w=400&h=300&fit=crop&crop=center', // Wedding dance
                         'https://images.unsplash.com/photo-1519741497674-611481863552?w=400&h=300&fit=crop&crop=center'  // Wedding party
                     ];
                     
                     // Generate the exact number of items based on the loop
                     for ($i = $startValue; $i <= $endValue; $i++) {
                         $photoIndex = ($i - $startValue) % count($galleryPhotos); // Cycle through photos if needed
                         $photoUrl = $previewData["gallery_photo_$i"] ?? $galleryPhotos[$photoIndex];
                         $galleryContent .= '                <div class="gallery-item fade-in">' . "\n";
                         $galleryContent .= '                    <img src="' . $photoUrl . '" alt="Gallery Photo ' . $i . '">' . "\n";
                         $galleryContent .= '                </div>' . "\n";
                     }
                     
                     file_put_contents(storage_path('logs/debug_test.txt'), "Generated " . ($endValue - $startValue + 1) . " gallery items\n", FILE_APPEND);
                 } else {
                     // Fallback: if we can't parse the loop, use the old method
                     file_put_contents(storage_path('logs/debug_test.txt'), "Could not parse @for loop, using fallback\n", FILE_APPEND);
                     $galleryContent = '';
                     for ($i = 1; $i <= 6; $i++) {
                         $photoUrl = $previewData["gallery_photo_$i"] ?? 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=400&h=300&fit=crop&crop=center';
                         $galleryContent .= '                <div class="gallery-item fade-in">' . "\n";
                         $galleryContent .= '                    <img src="' . $photoUrl . '" alt="Gallery Photo ' . $i . '">' . "\n";
                         $galleryContent .= '                </div>' . "\n";
                     }
                 }
                 
                 // Replace the exact section
                 $beforeReplace = strpos($htmlContent, '@for') !== false;
                 $htmlContent = str_replace($forSection, $galleryContent, $htmlContent);
                 $afterReplace = strpos($htmlContent, '@for') !== false;
                 
                 file_put_contents(storage_path('logs/debug_test.txt'), "Before replace @for exists: " . ($beforeReplace ? 'YES' : 'NO') . "\n", FILE_APPEND);
                 file_put_contents(storage_path('logs/debug_test.txt'), "After replace @for exists: " . ($afterReplace ? 'YES' : 'NO') . "\n", FILE_APPEND);
             }
        }
        
        // ALWAYS add gallery photos (even if previewData exists)
        for ($i = 1; $i <= 6; $i++) {
            if (!isset($previewData["gallery_photo_$i"])) {
                $previewData["gallery_photo_$i"] = 'https://images.unsplash.com/photo-' . rand(1500000000000, 1600000000000) . '?w=400&h=300&fit=crop';
            }
        }
        
        // If no default variables exist, provide comprehensive fallback data
        if (empty($template->default_variables)) {
            $previewData = [
                'bride_name' => 'Fatimah',
                'groom_name' => 'Ahmad',
                'bride_full_name' => 'Fatimah Binti Abdullah',
                'groom_full_name' => 'Ahmad Bin Ibrahim',
                'wedding_date' => date('j F Y'),
                'venue' => 'Dewan Serbaguna Komuniti',
                'address' => 'Jalan Taman Melati, 53100 Kuala Lumpur',
                'bride_photo' => 'https://images.unsplash.com/photo-1494790108755-2616c9c0b33d?w=300&h=300&fit=crop&crop=face',
                'groom_photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=300&fit=crop&crop=face',
                'akad_date' => 'Friday, ' . date('j F Y', strtotime('+1 day')),
                'akad_time' => '10:00 AM',
                'reception_date' => 'Saturday, ' . date('j F Y', strtotime('+2 days')),
                'reception_time' => '12:00 PM - 5:00 PM',
                'reception_title' => 'Majlis Bersanding & Reception',
                'reception_description' => 'The grand celebration featuring the traditional Malaysian wedding throne ceremony, followed by a feast and entertainment.',
                'groom_reception_date' => 'Sunday, ' . date('j F Y', strtotime('+3 days')),
                'groom_reception_time' => '11:00 AM - 3:00 PM',
                'groom_reception_title' => 'Majlis Bertandang (Groom\'s Reception)',
                'groom_reception_description' => 'A second reception hosted by the groom\'s family, featuring traditional Malaysian cuisine and customs.',
                'groom_reception_venue' => 'Dewan Komuniti Taman Sari',
                'venue_description' => 'The wedding reception will be held at this beautiful venue with ample parking and easy access.',
                'footer_message' => 'Thank you for celebrating our special day with us!',
                'bride_father' => 'Abdullah Bin Ibrahim',
                'bride_mother' => 'Zainab Binti Ali',
                'groom_father' => 'Mohammad Bin Hassan',
                'groom_mother' => 'Khadijah Binti Ahmad',
                // Video URLs for testing
                'wedding_invitation_video' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
                'wedding_invitation_video_horizontal' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
                'wedding_invitation_video_vertical' => 'https://sample-videos.com/zip/10/mp4/360/SampleVideo_360x640_1mb.mp4',
                'video_poster' => 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=1920&h=1080&fit=crop',
                'video_poster_horizontal' => 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=1920&h=1080&fit=crop',
                'video_poster_vertical' => 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=1080&h=1920&fit=crop',
                'video_subtitle' => 'A Special Message for You'
            ];
            
            // Add gallery photos
            for ($i = 1; $i <= 6; $i++) {
                $previewData["gallery_photo_$i"] = 'https://images.unsplash.com/photo-' . rand(1500000000000, 1600000000000) . '?w=400&h=300&fit=crop';
            }
        }

        // Use the same comprehensive template processing as the preview page
        $details = $previewData;
        
        // Handle $weddingCard variables using real template data
        // For preview, we use the template itself as the wedding card context
        $weddingCardData = [
            'id' => $template->id,
            'unique_url' => 'preview-' . $template->id,
            'title' => $template->name,
            'is_active' => $template->is_active,
            'created_at' => $template->created_at,
            'updated_at' => $template->updated_at
        ];
        
        // Handle $weddingCard->id references
        $htmlContent = preg_replace_callback(
            '/\{\{\s*\$weddingCard->id\s*\?\?\s*[\'"]?([^\}\'"]*)[\'"]*\s*\}\}/',
            function($matches) use ($weddingCardData) {
                return $weddingCardData['id'];
            },
            $htmlContent
        );
        
        // Handle other $weddingCard properties
        $htmlContent = preg_replace_callback(
            '/\{\{\s*\$weddingCard->([a-zA-Z_]+)\s*\?\?\s*[\'"]?([^\}\'"]*)[\'"]*\s*\}\}/',
            function($matches) use ($weddingCardData) {
                $property = $matches[1];
                $fallback = $matches[2] ?? '';
                return $weddingCardData[$property] ?? $fallback;
            },
            $htmlContent
        );
        
        // Handle simple $weddingCard->property without fallback
        $htmlContent = preg_replace_callback(
            '/\{\{\s*\$weddingCard->([a-zA-Z_]+)\s*\}\}/',
            function($matches) use ($weddingCardData) {
                $property = $matches[1];
                return $weddingCardData[$property] ?? '';
            },
            $htmlContent
        );
        
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
        
        // Handle substr() function
        $htmlContent = preg_replace_callback(
            '/\{\{\s*substr\(\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*,\s*(\d+)\s*,\s*(\d+)\)\s*\}\}/',
            function($matches) use ($previewData) {
                $key = $matches[1];
                $fallback = $matches[2];
                $start = (int)$matches[3];
                $length = (int)$matches[4];
                return substr($previewData[$key] ?? $fallback, $start, $length);
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
        
        // Handle csrf_token() function
        $htmlContent = preg_replace_callback(
            '/\{\{\s*csrf_token\(\)\s*\}\}/',
            function($matches) {
                return csrf_token();
            },
            $htmlContent
        );
        
        // Handle @csrf directive
        $htmlContent = str_replace('@csrf', '<input type="hidden" name="_token" value="' . csrf_token() . '">', $htmlContent);
        
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
        
        // Handle simple Blade variables without fallback (double quotes)
        $htmlContent = preg_replace_callback(
            '/\{\{\s*\$details\["([^"]+)"\]\s*\}\}/',
            function($matches) use ($previewData) {
                $key = $matches[1];
                return $previewData[$key] ?? '';
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
        
        // Process @if/@else/@endif blocks FIRST before simple @if blocks
        
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
            '/@if\(\$details\["([^"]+)"\]\)(.*?)@else(.*?)@endif/s',
            function($matches) use ($previewData) {
                $key = $matches[1];
                $ifContent = $matches[2];
                $elseContent = $matches[3];
                return !empty($previewData[$key]) ? $ifContent : $elseContent;
            },
                    $htmlContent
                );
        
        // Handle simple @if statements (single quotes)
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
        
        // DEBUG: Let's see what's actually happening
        error_log("=== DEBUG FULLPREVIEW ===");
        error_log("Template ID: " . $template->id);
        error_log("Has full_html_template: " . (!empty($template->full_html_template) ? 'YES' : 'NO'));
        error_log("@for found in content: " . (strpos($htmlContent, '@for') !== false ? 'YES' : 'NO'));
        error_log("Content length: " . strlen($htmlContent));
        

        
        // @for processing moved to the beginning - this section is now cleaned up
        
        // NOW do cleanup operations AFTER @for processing
        // Clean up any remaining orphaned @endif statements
        $htmlContent = str_replace('@endif', '', $htmlContent);
        
        // Clean up any remaining orphaned @if statements  
        $htmlContent = preg_replace('/@if\([^)]+\)/', '', $htmlContent);
        
        // Clean up any remaining orphaned @else statements
        $htmlContent = str_replace('@else', '', $htmlContent);
        
        // Handle & symbol combining
        $htmlContent = preg_replace_callback(
            '/\{\{\s*substr\(\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*,\s*(\d+)\s*,\s*(\d+)\)\s*\}\}\s*&\s*\{\{\s*substr\(\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*,\s*(\d+)\s*,\s*(\d+)\)\s*\}\}/',
            function($matches) use ($previewData) {
                $key1 = $matches[1];
                $fallback1 = $matches[2];
                $start1 = (int)$matches[3];
                $length1 = (int)$matches[4];
                $key2 = $matches[5];
                $fallback2 = $matches[6];
                $start2 = (int)$matches[7];
                $length2 = (int)$matches[8];
                
                $value1 = substr($previewData[$key1] ?? $fallback1, $start1, $length1);
                $value2 = substr($previewData[$key2] ?? $fallback2, $start2, $length2);
                
                return $value1 . ' & ' . $value2;
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
        
        // Handle dates with double quotes as well
        $htmlContent = preg_replace_callback(
            '/\{\{\s*date\("([^"]+)"\)\s*\}\}/',
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
        
        // // Clean up any remaining problematic Blade syntax that could cause JavaScript errors
        // // This cleanup happens AFTER all specific pattern processing to catch any missed expressions
        
        // // Remove any remaining unprocessed object references that could break JavaScript
        // $htmlContent = preg_replace('/\{\{\s*\$[a-zA-Z_][a-zA-Z0-9_]*->[a-zA-Z_][a-zA-Z0-9_]*[^}]*\}\}/', 'null', $htmlContent);
        
        // // Remove any remaining array access patterns
        // $htmlContent = preg_replace('/\{\{\s*\$[a-zA-Z_][a-zA-Z0-9_]*\[[^\]]*\][^}]*\}\}/', '""', $htmlContent);
        
        // // Remove any remaining null coalescing expressions
        // $htmlContent = preg_replace('/\{\{\s*[^}]*\?\?\s*[^}]*\}\}/', '""', $htmlContent);
        
        // // Remove any remaining simple variable references
        // $htmlContent = preg_replace('/\{\{\s*\$[a-zA-Z_][a-zA-Z0-9_]*\s*\}\}/', '""', $htmlContent);
        
        // // Final cleanup: remove any remaining malformed Blade expressions
        // $htmlContent = preg_replace('/\{\{[^}]*\}\}/', '""', $htmlContent);
        
        // // ADMIN PREVIEW: Replace gift functionality with preview-only behavior
        // // Since this is just a template preview, disable actual gift submission
        // $htmlContent = preg_replace(
        //     '/window\.weddingCardId\s*=\s*[^;]+;/',
        //     'window.weddingCardId = null; // Admin preview - gift disabled',
        //     $htmlContent
        // );
        
        // // Replace sendGift function to show preview message instead of submitting
        // $previewGiftFunction = "
        // function sendGift() {
        //     alert('This is a template preview. Gift functionality is disabled in preview mode.');
        //     closeGiftModal();
        // }";
        
        // if (strpos($htmlContent, 'function sendGift()') !== false) {
        //     $htmlContent = preg_replace(
        //         '/function sendGift\(\)\s*\{[^}]*\}/',
        //         $previewGiftFunction,
        //         $htmlContent
        //     );
        // }

        // FINAL DEBUG: Log what we're actually returning
        error_log("=== FINAL HTML SAMPLE ===");
        $galleryPos = strpos($htmlContent, 'Our Moments');
        if ($galleryPos !== false) {
            $sample = substr($htmlContent, $galleryPos, 1000);
            error_log("Gallery section sample: " . $sample);
        }
        error_log("=== END DEBUG ===");
        
        // Return the processed HTML directly
        return response($htmlContent)
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Upload photo for template variables.
     */
    public function uploadVariablePhoto(Request $request)
    {
        try {
            $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'variable_name' => 'required|string|max:255',
                'template_id' => 'nullable|integer',
                'old_url' => 'nullable|string',
            ]);

            $variableName = $request->get('variable_name');
            $file = $request->file('photo');
            $oldUrl = $request->get('old_url');
            
            // Delete old file if provided
            if ($oldUrl) {
                $this->deleteFileFromUrl($oldUrl);
            }
            
            // Generate filename with variable name prefix
            $filename = 'template-var-' . $variableName . '-' . time() . '.' . $file->getClientOriginalExtension();
            
            // Store in template-variables folder
            $path = $file->storeAs('template-variables', $filename, 'public');
            
            // Return the public URL
            $url = Storage::url($path);
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => $path,
                'variable_name' => $variableName
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload video for template variables.
     */
    public function uploadVariableVideo(Request $request)
    {
        try {
            $request->validate([
                'video' => 'required|file|mimes:mp4,mov,avi,wmv,flv,webm|max:51200', // 50MB max
                'variable_name' => 'required|string|max:255',
                'template_id' => 'nullable|integer',
                'old_url' => 'nullable|string',
            ]);

            $variableName = $request->get('variable_name');
            $file = $request->file('video');
            $oldUrl = $request->get('old_url');
            
            // Delete old file if provided
            if ($oldUrl) {
                $this->deleteFileFromUrl($oldUrl);
            }
            
            // Generate filename with variable name prefix
            $filename = 'template-var-' . $variableName . '-' . time() . '.' . $file->getClientOriginalExtension();
            
            // Store in template-variables folder
            $path = $file->storeAs('template-variables', $filename, 'public');
            
            // Return the public URL
            $url = Storage::url($path);
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => $path,
                'variable_name' => $variableName
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload audio for template variables.
     */
    public function uploadVariableAudio(Request $request)
    {
        try {
            $request->validate([
                'audio' => 'required|file|mimes:mp3,wav,ogg,aac,m4a|max:10240', // 10MB max
                'variable_name' => 'required|string|max:255',
                'template_id' => 'nullable|integer',
                'old_url' => 'nullable|string',
            ]);

            $variableName = $request->get('variable_name');
            $file = $request->file('audio');
            $oldUrl = $request->get('old_url');
            
            // Delete old file if provided
            if ($oldUrl) {
                $this->deleteFileFromUrl($oldUrl);
            }
            
            // Generate filename with variable name prefix
            $filename = 'template-var-' . $variableName . '-' . time() . '.' . $file->getClientOriginalExtension();
            
            // Store in template-variables folder
            $path = $file->storeAs('template-variables', $filename, 'public');
            
            // Return the public URL
            $url = Storage::url($path);
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => $path,
                'variable_name' => $variableName
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a variable file (photo or video).
     */
    public function deleteVariableFile(Request $request)
    {
        try {
            Log::info('Delete file request received', [
                'url' => $request->get('file_url'),
                'method' => $request->method(),
                'all_data' => $request->all()
            ]);

            $request->validate([
                'file_url' => 'required|string',
            ]);

            $fileUrl = $request->get('file_url');
            Log::info('Processing file deletion for: ' . $fileUrl);
            
            // Only delete files from our storage to prevent abuse
            if (strpos($fileUrl, '/storage/') === false) {
                Log::warning('Attempted to delete non-storage file: ' . $fileUrl);
                return response()->json([
                    'success' => false,
                    'message' => 'Can only delete files from application storage'
                ], 400);
            }
            
            // Delete the file
            $deleted = $this->deleteFileFromUrl($fileUrl);
            Log::info('File deletion result: ' . ($deleted ? 'success' : 'failed'));
            
            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully',
                'deleted' => $deleted
            ]);

        } catch (\Exception $e) {
            Log::error('Delete file error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }
} 