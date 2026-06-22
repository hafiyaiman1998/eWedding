<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteVariableFileRequest;
use App\Http\Requests\StoreTemplateRequest;
use App\Http\Requests\UpdateTemplateRequest;
use App\Http\Requests\UploadVariableAudioRequest;
use App\Http\Requests\UploadVariablePhotoRequest;
use App\Http\Requests\UploadVariableVideoRequest;
use App\Models\DesignTemplate;
use App\Services\FileUploadService;
use App\Services\TemplateRenderer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminTemplateController extends Controller
{
    public function __construct(
        private TemplateRenderer $templateRenderer,
        private FileUploadService $fileUploadService,
    ) {}

    /**
     * Display a listing of the design templates.
     */
    public function index(Request $request): View
    {
        $query = DesignTemplate::withCount('weddingCards');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
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
    public function malaysian(): View
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
    public function create(): View
    {
        return view('admin.templates.create');
    }

    /**
     * Store a newly created design template in storage.
     */
    public function store(StoreTemplateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $templateData = $validated;
        $templateData['is_malaysian_design'] = $request->boolean('is_malaysian_design');

        // Handle preview image upload
        if ($request->hasFile('preview_image')) {
            $path = $request->file('preview_image')->store('template-previews', 'public');
            $templateData['preview_image'] = $path;
        }

        // Parse default variables
        if (! empty($validated['default_variables'])) {
            $templateData['default_variables'] = json_decode($validated['default_variables'], true);
        }

        DesignTemplate::create($templateData);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Design template created successfully.');
    }

    /**
     * Display the specified design template.
     */
    public function show(DesignTemplate $template): View
    {
        $template->load(['weddingCards.user']);

        return view('admin.templates.show', compact('template'));
    }

    /**
     * Show the form for editing the specified design template.
     */
    public function edit(DesignTemplate $template): View
    {
        return view('admin.templates.edit', compact('template'));
    }

    /**
     * Update the specified design template in storage.
     */
    public function update(UpdateTemplateRequest $request, DesignTemplate $template): RedirectResponse
    {
        $validated = $request->validated();

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
        if (! empty($validated['default_variables'])) {
            $newVariables = json_decode($validated['default_variables'], true);
        }

        // Check if parse variables was used for more aggressive cleanup
        $parseVariablesUsed = $request->boolean('parse_variables_used');

        // Clean up unused materials
        $this->cleanupUnusedMaterials($template, $oldVariables, $newVariables, $parseVariablesUsed);

        // Parse default variables
        if (! empty($validated['default_variables'])) {
            $templateData['default_variables'] = $newVariables;
        }

        $template->update($templateData);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Design template updated successfully.');
    }

    /**
     * Clean up unused materials when variables are updated.
     */
    private function cleanupUnusedMaterials(DesignTemplate $template, array $oldVariables, array $newVariables, bool $parseVariablesUsed = false): void
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
    private function cleanupOrphanedTemplateFiles(DesignTemplate $template, array $currentFileUrls): void
    {
        // Get all files in the template-variables directory
        $allTemplateFiles = Storage::disk('public')->allFiles('template-variables');

        foreach ($allTemplateFiles as $filePath) {
            $fileUrl = Storage::url($filePath);

            // If this file is not in the current variables list, it's orphaned
            if (! in_array($fileUrl, $currentFileUrls)) {
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
        ) && ! empty(trim($url));
    }

    /**
     * Delete a file from URL.
     */
    private function deleteFileFromUrl(string $url): bool
    {
        return $this->fileUploadService->deleteByUrl($url);
    }

    /**
     * Remove the specified design template from storage.
     */
    public function destroy(Request $request, DesignTemplate $template): JsonResponse|RedirectResponse
    {
        try {
            // Check if template is being used
            $weddingCardsCount = $template->weddingCards()->count();
            if ($weddingCardsCount > 0) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot delete template as it is being used by {$weddingCardsCount} wedding cards.",
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
                    'redirect_url' => route('admin.templates.index'),
                ]);
            }

            return redirect()->route('admin.templates.index')
                ->with('success', "Template '{$templateName}' has been deleted successfully.");

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the template. Please try again.',
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'An error occurred while deleting the template.');
        }
    }

    /**
     * Preview the template with real default variables data.
     */
    public function preview(DesignTemplate $template): View
    {
        // Use real data from template's default_variables
        $previewData = $template->default_variables ?? [];

        // If no default variables exist, provide minimal fallback
        if (empty($previewData)) {
            $previewData = [
                'bride_name' => 'Bride Name',
                'groom_name' => 'Groom Name',
                'wedding_date' => date('j F Y'),
                'venue' => 'Wedding Venue',
            ];
        }

        return view('admin.templates.preview', compact('template', 'previewData'));
    }

    /**
     * Show the full HTML template with animations in a new tab.
     */
    public function fullPreview(DesignTemplate $template): Response|JsonResponse
    {
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

        // Use real data from template's default_variables
        $previewData = $template->default_variables ?? [];

        // ALWAYS add gallery photos (even if previewData exists)
        for ($i = 1; $i <= 6; $i++) {
            if (! isset($previewData["gallery_photo_$i"])) {
                $previewData["gallery_photo_$i"] = 'https://images.unsplash.com/photo-'.rand(1500000000000, 1600000000000).'?w=400&h=300&fit=crop';
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
                'akad_date' => 'Friday, '.date('j F Y', strtotime('+1 day')),
                'akad_time' => '10:00 AM',
                'reception_date' => 'Saturday, '.date('j F Y', strtotime('+2 days')),
                'reception_time' => '12:00 PM - 5:00 PM',
                'reception_title' => 'Majlis Bersanding & Reception',
                'reception_description' => 'The grand celebration featuring the traditional Malaysian wedding throne ceremony, followed by a feast and entertainment.',
                'groom_reception_date' => 'Sunday, '.date('j F Y', strtotime('+3 days')),
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
                'video_subtitle' => 'A Special Message for You',
            ];

            // Add gallery photos
            for ($i = 1; $i <= 6; $i++) {
                $previewData["gallery_photo_$i"] = 'https://images.unsplash.com/photo-'.rand(1500000000000, 1600000000000).'?w=400&h=300&fit=crop';
            }
        }

        // Handle $weddingCard variables using real template data.
        // For preview, we use the template itself as the wedding card context.
        $weddingCardData = [
            'id' => $template->id,
            'unique_url' => 'preview-'.$template->id,
            'title' => $template->name,
            'is_active' => $template->is_active,
            'created_at' => $template->created_at,
            'updated_at' => $template->updated_at,
        ];

        $htmlContent = $this->templateRenderer->render($htmlContent, $previewData, [
            'wedding_card' => $weddingCardData,
            'process_csrf' => true,
            'combine_ampersand' => true,
            'substr_before_date' => true,
            'double_quoted_first' => true,
        ]);

        // Return the processed HTML directly
        return response($htmlContent)
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Upload photo for template variables.
     */
    public function uploadVariablePhoto(UploadVariablePhotoRequest $request): JsonResponse
    {
        try {
            $variableName = $request->get('variable_name');
            $file = $request->file('photo');
            $oldUrl = $request->get('old_url');

            // Delete old file if provided
            if ($oldUrl) {
                $this->fileUploadService->deleteByUrl($oldUrl);
            }

            // Store in template-variables folder
            $path = $this->fileUploadService->storeTemplateVariableFile($file, $variableName);

            // Return the public URL
            $url = Storage::url($path);

            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => $path,
                'variable_name' => $variableName,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload video for template variables.
     */
    public function uploadVariableVideo(UploadVariableVideoRequest $request): JsonResponse
    {
        try {
            $variableName = $request->get('variable_name');
            $file = $request->file('video');
            $oldUrl = $request->get('old_url');

            // Delete old file if provided
            if ($oldUrl) {
                $this->fileUploadService->deleteByUrl($oldUrl);
            }

            // Store in template-variables folder
            $path = $this->fileUploadService->storeTemplateVariableFile($file, $variableName);

            // Return the public URL
            $url = Storage::url($path);

            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => $path,
                'variable_name' => $variableName,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload audio for template variables.
     */
    public function uploadVariableAudio(UploadVariableAudioRequest $request): JsonResponse
    {
        try {
            $variableName = $request->get('variable_name');
            $file = $request->file('audio');
            $oldUrl = $request->get('old_url');

            // Delete old file if provided
            if ($oldUrl) {
                $this->fileUploadService->deleteByUrl($oldUrl);
            }

            // Store in template-variables folder
            $path = $this->fileUploadService->storeTemplateVariableFile($file, $variableName);

            // Return the public URL
            $url = Storage::url($path);

            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => $path,
                'variable_name' => $variableName,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a variable file (photo or video).
     */
    public function deleteVariableFile(DeleteVariableFileRequest $request): JsonResponse
    {
        try {
            Log::info('Delete file request received', [
                'url' => $request->get('file_url'),
                'method' => $request->method(),
                'all_data' => $request->all(),
            ]);

            $fileUrl = $request->get('file_url');
            Log::info('Processing file deletion for: '.$fileUrl);

            // Only delete files from our storage to prevent abuse
            if (strpos($fileUrl, '/storage/') === false) {
                Log::warning('Attempted to delete non-storage file: '.$fileUrl);

                return response()->json([
                    'success' => false,
                    'message' => 'Can only delete files from application storage',
                ], 400);
            }

            // Delete the file
            $deleted = $this->deleteFileFromUrl($fileUrl);
            Log::info('File deletion result: '.($deleted ? 'success' : 'failed'));

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully',
                'deleted' => $deleted,
            ]);

        } catch (\Exception $e) {
            Log::error('Delete file error: '.$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Delete failed: '.$e->getMessage(),
            ], 500);
        }
    }
}
