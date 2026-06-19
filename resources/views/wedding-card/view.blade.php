<?php

// Get card details
$details = $card->card_details ?? [];

// Define all possible media file fields that need URL conversion
$mediaFields = [
    // Image fields - Single couple (legacy)
    'bride_photo', 'groom_photo', 'after_photo', 'before_photo', 'video_poster', 'qr_code_image',
    // Image fields - Multiple couples (new)
    'bride_1_photo', 'groom_1_photo', 'bride_2_photo', 'groom_2_photo',
    'bride_3_photo', 'groom_3_photo', 'bride_4_photo', 'groom_4_photo',
    'hero_image', 'background_image', 'rsvp_bg',
    'story_1', 'story_2', 'story_3', 'story_4',
    // Audio fields
    'song_1_url', 'song_2_url', 'song_3_url', 'song_4_url', 'song_5_url',
    // Video fields
    'wedding_invitation_video', 'wedding_invitation_video_horizontal', 'wedding_invitation_video_vertical',
    'video_poster_horizontal', 'video_poster_vertical',
    // Gallery photos
    'gallery_photo_1', 'gallery_photo_2', 'gallery_photo_3', 'gallery_photo_4', 'gallery_photo_5', 'gallery_photo_6'
];

// Convert all media file paths to full URLs
foreach ($mediaFields as $field) {
    if (isset($details[$field]) && !empty($details[$field])) {
        // Convert stored file path to full URL if it's not already a URL
        if (!str_starts_with($details[$field], 'http')) {
            $details[$field] = asset('storage/' . $details[$field]);
        }
    }
}

// Add placeholder gallery photos for display if user hasn't uploaded any
for ($i = 1; $i <= 6; $i++) {
    if (!isset($details["gallery_photo_$i"]) || empty($details["gallery_photo_$i"])) {
        $details["gallery_photo_$i"] = 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&w=300&h=300&fit=crop';
    }
}

// Prepare Open Graph metadata for rich previews (e.g., WhatsApp, FB)
// Support both old (bride_name, groom_name) and new (bride_1_name, etc.) naming conventions
$brideName = $details['bride_name'] ?? $details['bride_1_name'] ?? '';
$groomName = $details['groom_name'] ?? $details['groom_1_name'] ?? '';

// For multiple couples, build a combined title
$coupleNames = [];
if (!empty($details['groom_1_name']) || !empty($details['bride_1_name'])) {
    // Using new multi-couple format
    $couple1 = trim(($details['groom_1_name'] ?? '') . ' & ' . ($details['bride_1_name'] ?? ''));
    if ($couple1 && $couple1 !== '&') $coupleNames[] = $couple1;

    if (!empty($details['groom_2_name']) || !empty($details['bride_2_name'])) {
        $couple2 = trim(($details['groom_2_name'] ?? '') . ' & ' . ($details['bride_2_name'] ?? ''));
        if ($couple2 && $couple2 !== '&') $coupleNames[] = $couple2;
    }

    if (!empty($details['groom_3_name']) || !empty($details['bride_3_name'])) {
        $couple3 = trim(($details['groom_3_name'] ?? '') . ' & ' . ($details['bride_3_name'] ?? ''));
        if ($couple3 && $couple3 !== '&') $coupleNames[] = $couple3;
    }

    if (!empty($details['groom_4_name']) || !empty($details['bride_4_name'])) {
        $couple4 = trim(($details['groom_4_name'] ?? '') . ' & ' . ($details['bride_4_name'] ?? ''));
        if ($couple4 && $couple4 !== '&') $coupleNames[] = $couple4;
    }
}

$weddingDateRaw = $details['wedding_date'] ?? '';
if ($weddingDateRaw) {
    try {
        // Parse the date and format as "7 Disember 2025, Ahad"
        $carbonDate = \Carbon\Carbon::parse($weddingDateRaw);
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Mac', 4 => 'April', 5 => 'Mei', 6 => 'Jun',
            7 => 'Julai', 8 => 'Ogos', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Disember'
        ];
        $hari = [
            'Sunday' => 'Ahad', 'Monday' => 'Isnin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Khamis', 'Friday' => 'Jumaat', 'Saturday' => 'Sabtu'
        ];
        $day = $carbonDate->format('j');
        $month = $bulan[(int)$carbonDate->format('n')];
        $year = $carbonDate->format('Y');
        $weekday = $hari[$carbonDate->format('l')];
        $weddingDateRaw = "{$day} {$month} {$year}, {$weekday}";
        // Update the details array so the template uses the formatted date
        $details['wedding_date'] = $weddingDateRaw;
    } catch (\Exception $e) {
        // fallback to raw if parsing fails
    }
}
try {
    $weddingDateFormatted = $weddingDateRaw ? \Carbon\Carbon::parse($weddingDateRaw)->format('d M Y') : '';
} catch (\Exception $e) {
    $weddingDateFormatted = $weddingDateRaw;
}

$ogTitleParts = [];
// Use multi-couple format if available, otherwise fall back to single couple
if (!empty($coupleNames)) {
    $ogTitleParts[] = implode(' and ', $coupleNames);
} elseif ($brideName || $groomName) {
    $ogTitleParts[] = trim(($groomName ?: ' ') . ' & ' . ($brideName ?: ' '));
}
if ($weddingDateFormatted) { $ogTitleParts[] = $weddingDateFormatted; }
$ogTitle = $ogTitleParts ? ('Undangan ' . implode(' | ', $ogTitleParts)) : ($card->title ?: config('app.name'));

$ogDescription = 'Tekan pautan untuk lihat kad jemputan. Mengandungi lokasi, aturcara dan RSVP.';

$ogImage = asset('asset/background/front_page_vertical.jpg')
    ?? $details['background_image']
    ?? $details['hero_image']
    ?? $details['video_poster']
    ?? $details['gallery_photo_1']
    ?? $details['bride_photo']
    ?? $details['groom_photo']
    ?? $details['bride_1_photo']
    ?? $details['groom_1_photo']
    ?? $details['bride_2_photo']
    ?? $details['groom_2_photo']
    ?? asset('favicon.ico');

$ogUrl = request()->fullUrl();

// Process RSVP messages for display in template
$rsvpMessagesJson = json_encode($rsvpMessages->map(function($rsvp) {
    return [
        'guest_name' => $rsvp->guest_name,
        'message' => $rsvp->message,
        'created_at' => $rsvp->created_at->format('M d, Y')
    ];
})->toArray());

// Get the template content
$templateContent = $card->designTemplate->full_html_template ?? $card->designTemplate->blade_template ?? '';

// If we're here, we know we have some template content
if ($templateContent) {
    // Process @for loops FIRST - completely replace them with actual HTML like AdminTemplateController
    if (strpos($templateContent, '@for') !== false) {
        $forPos = strpos($templateContent, '@for');
        $endforPos = strpos($templateContent, '@endfor', $forPos);
        
        if ($endforPos !== false) {
            $forSection = substr($templateContent, $forPos, $endforPos - $forPos + 7);
            
            // Parse the @for loop to extract parameters
            if (preg_match('/@for\s*\(\s*\$(\w+)\s*=\s*(\d+);\s*\$\w+\s*<=\s*(\d+);\s*\$\w+\+\+\s*\)/', $forSection, $matches)) {
                $startValue = (int)$matches[2];
                $endValue = (int)$matches[3];
                
                // Generate gallery content completely
                $galleryContent = '';
                for ($i = $startValue; $i <= $endValue; $i++) {
                    $photoUrl = $details["gallery_photo_$i"];
                    $galleryContent .= '                <div class="gallery-item fade-in">' . "\n";
                    $galleryContent .= '                    <img src="' . $photoUrl . '" alt="Gallery Photo ' . $i . '">' . "\n";
                    $galleryContent .= '                </div>' . "\n";
                }
                
                // Replace the entire @for section with rendered HTML
                $templateContent = str_replace($forSection, $galleryContent, $templateContent);
            }
        }
    }
    
    // Process PHP functions like strtoupper() with Blade variables
    $templateContent = preg_replace_callback(
        '/\{\{\s*strtoupper\(\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\)\s*\}\}/',
        function($matches) use ($details) {
            $key = $matches[1];
            $fallback = $matches[2];
            return strtoupper($details[$key] ?? $fallback);
        },
        $templateContent
    );
    
    // Handle date() function
    $templateContent = preg_replace_callback(
        '/\{\{\s*date\("([^"]+)"\)\s*\}\}/',
        function($matches) {
            $format = $matches[1];
            return date($format);
        },
        $templateContent
    );
    
    // Handle csrf_token() function
    $templateContent = preg_replace_callback(
        '/\{\{\s*csrf_token\(\)\s*\}\}/',
        function($matches) {
            return csrf_token();
        },
        $templateContent
    );
    
    // Handle @csrf directive
    $templateContent = str_replace('@csrf', '<input type="hidden" name="_token" value="' . csrf_token() . '">', $templateContent);
    
    // Handle $weddingCard variables using real wedding card data
    $weddingCardData = [
        'id' => $card->id,
        'unique_url' => $card->unique_url,
        'title' => $card->title,
        'is_active' => $card->is_published,
        'is_published' => $card->is_published,
        'created_at' => $card->created_at,
        'updated_at' => $card->updated_at
    ];
    
    // Ensure the JavaScript gets the correct wedding card ID by adding it to the template
    $templateContent = str_replace(
        'window.weddingCardId = {{ $weddingCard->id ?? \'null\' }};',
        'window.weddingCardId = ' . $card->id . ';',
        $templateContent
    );
    
    // Handle $weddingCard->id references
    $templateContent = preg_replace_callback(
        '/\{\{\s*\$weddingCard->id\s*\?\?\s*[\'"]?([^\}\'"]*)[\'"]*\s*\}\}/',
        function($matches) use ($weddingCardData) {
            return $weddingCardData['id'];
        },
        $templateContent
    );
    
    // Handle other $weddingCard properties
    $templateContent = preg_replace_callback(
        '/\{\{\s*\$weddingCard->([a-zA-Z_]+)\s*\?\?\s*[\'"]?([^\}\'"]*)[\'"]*\s*\}\}/',
        function($matches) use ($weddingCardData) {
            $property = $matches[1];
            $fallback = $matches[2] ?? '';
            return $weddingCardData[$property] ?? $fallback;
        },
        $templateContent
    );
    
    // Handle simple $weddingCard->property without fallback
    $templateContent = preg_replace_callback(
        '/\{\{\s*\$weddingCard->([a-zA-Z_]+)\s*\}\}/',
        function($matches) use ($weddingCardData) {
            $property = $matches[1];
            return $weddingCardData[$property] ?? '';
        },
        $templateContent
    );
    
    // Handle substr() functions with Blade variables
    $templateContent = preg_replace_callback(
        '/\{\{\s*substr\(\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*,\s*(\d+)\s*,\s*(\d+)\)\s*\}\}/',
        function($matches) use ($details) {
            $key = $matches[1];
            $fallback = $matches[2];
            $start = (int)$matches[3];
            $length = (int)$matches[4];
            $value = $details[$key] ?? $fallback;
            return substr($value, $start, $length);
        },
        $templateContent
    );
    
    // Handle complex Blade variables with multiple fallbacks (mixed quotes)
    $templateContent = preg_replace_callback(
        '/\{\{\s*\$details\[\'([^\']+)\'\]\s*\?\?\s*\$details\[\'([^\']+)\'\]\s*\?\?\s*\'([^\']*)\'\s*\}\}/',
        function($matches) use ($details) {
            $key1 = $matches[1];
            $key2 = $matches[2];
            $fallback = $matches[3];
            return $details[$key1] ?? $details[$key2] ?? $fallback;
        },
        $templateContent
    );
    
    // Handle complex Blade variables with multiple fallbacks (double quotes)
    $templateContent = preg_replace_callback(
        '/\{\{\s*\$details\["([^"]+)"\]\s*\?\?\s*\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*\}\}/',
        function($matches) use ($details) {
            $key1 = $matches[1];
            $key2 = $matches[2];
            $fallback = $matches[3];
            return $details[$key1] ?? $details[$key2] ?? $fallback;
        },
        $templateContent
    );
    
    // Handle Blade variables with null coalescing operator (single quotes)
    $templateContent = preg_replace_callback(
        '/\{\{\s*\$details\[\'([^\']+)\'\]\s*\?\?\s*\'([^\']*)\'\s*\}\}/',
        function($matches) use ($details) {
            $key = $matches[1];
            $fallback = $matches[2];
            return $details[$key] ?? $fallback;
        },
        $templateContent
    );
    
    // Handle Blade variables with null coalescing operator (double quotes)
    $templateContent = preg_replace_callback(
        '/\{\{\s*\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*\}\}/',
        function($matches) use ($details) {
            $key = $matches[1];
            $fallback = $matches[2];
            return $details[$key] ?? $fallback;
        },
        $templateContent
    );
    
    // Handle simple Blade variables without fallback (single quotes)
    $templateContent = preg_replace_callback(
        '/\{\{\s*\$details\[\'([^\']+)\'\]\s*\}\}/',
        function($matches) use ($details) {
            $key = $matches[1];
            return $details[$key] ?? '';
        },
        $templateContent
    );
    
    // Handle simple Blade variables without fallback (double quotes)
    $templateContent = preg_replace_callback(
        '/\{\{\s*\$details\["([^"]+)"\]\s*\}\}/',
        function($matches) use ($details) {
            $key = $matches[1];
            return $details[$key] ?? '';
        },
        $templateContent
    );
    
    // Handle @if/@else/@endif conditionals for simple variables
    $templateContent = preg_replace_callback(
        '/@if\(\$details\["([^"]+)"\]\s*\?\?\s*false\)(.*?)@else(.*?)@endif/s',
        function($matches) use ($details) {
            $key = $matches[1];
            $ifContent = $matches[2];
            $elseContent = $matches[3];
            return !empty($details[$key]) ? $ifContent : $elseContent;
        },
        $templateContent
    );
    
    // Handle simple @if statements without @else
    $templateContent = preg_replace_callback(
        '/@if\(\$details\["([^"]+)"\]\s*\?\?\s*false\)(.*?)@endif/s',
        function($matches) use ($details) {
            $key = $matches[1];
            $content = $matches[2];
            return !empty($details[$key]) ? $content : '';
        },
        $templateContent
    );
    
    // Handle simple @if statements with isset()
    $templateContent = preg_replace_callback(
        '/@if\(isset\(\$details\[\'([^\']+)\'\]\)\)(.*?)@endif/s',
        function($matches) use ($details) {
            $key = $matches[1];
            $content = $matches[2];
            return isset($details[$key]) && !empty($details[$key]) ? $content : '';
        },
        $templateContent
    );
    
    // Handle simple @if statements (single quotes)
    $templateContent = preg_replace_callback(
        '/@if\(\$details\[\'([^\']+)\'\]\)(.*?)@endif/s',
        function($matches) use ($details) {
            $key = $matches[1];
            $content = $matches[2];
            return !empty($details[$key]) ? $content : '';
        },
        $templateContent
    );
    
    // Handle simple @if statements (double quotes)
    $templateContent = preg_replace_callback(
        '/@if\(\$details\["([^"]+)"\]\)(.*?)@endif/s',
        function($matches) use ($details) {
            $key = $matches[1];
            $content = $matches[2];
            return !empty($details[$key]) ? $content : '';
        },
        $templateContent
    );
    
    // Clean up any remaining orphaned @endif statements
    $templateContent = str_replace('@endif', '', $templateContent);
    
    // Clean up any remaining orphaned @if statements
    $templateContent = preg_replace('/@if\([^)]+\)/', '', $templateContent);
    
    // Clean up any remaining orphaned @else statements
    $templateContent = str_replace('@else', '', $templateContent);
    
    // Clean up any remaining orphaned @endfor statements
    $templateContent = str_replace('@endfor', '', $templateContent);
    
    // Clean up any remaining orphaned @for statements
    $templateContent = preg_replace('/@for\([^)]+\)/', '', $templateContent);
    
    // Handle any remaining {{ date('Y') }} expressions without quotes  
    $templateContent = preg_replace_callback(
        '/\{\{\s*date\(\'([^\']+)\'\)\s*\}\}/',
        function($matches) {
            $format = $matches[1];
            return date($format);
        },
        $templateContent
    );
    
    // Handle complex expressions like {{ date('Y') }} {{ $details["groom_name"] }}
    $templateContent = preg_replace_callback(
        '/\{\{\s*date\(\'([^\']+)\'\)\s*\}\}\s*\{\{\s*\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*\}\}/',
        function($matches) use ($details) {
            $dateFormat = $matches[1];
            $key = $matches[2];
            $fallback = $matches[3];
            return date($dateFormat) . ' ' . ($details[$key] ?? $fallback);
        },
        $templateContent
    );
    
    // Clean up any remaining Blade syntax that might cause issues
    $templateContent = str_replace('<?php', '', $templateContent);
    $templateContent = str_replace('?>', '', $templateContent);
    
    // Remove any leftover curly braces that might be malformed
    $templateContent = preg_replace('/\{\{\s*\}\}/', '', $templateContent);
    
    // Clean up any remaining problematic Blade syntax that could cause JavaScript errors
    // This cleanup happens AFTER all specific pattern processing to catch any missed expressions
    
    // Remove any remaining unprocessed object references that could break JavaScript
    $templateContent = preg_replace('/\{\{\s*\$[a-zA-Z_][a-zA-Z0-9_]*->[a-zA-Z_][a-zA-Z0-9_]*[^}]*\}\}/', 'null', $templateContent);
    
    // Remove any remaining array access patterns
    $templateContent = preg_replace('/\{\{\s*\$[a-zA-Z_][a-zA-Z0-9_]*\[[^\]]*\][^}]*\}\}/', '""', $templateContent);
    
    // Remove any remaining null coalescing expressions
    $templateContent = preg_replace('/\{\{\s*[^}]*\?\?\s*[^}]*\}\}/', '""', $templateContent);
    
    // Remove any remaining simple variable references
    $templateContent = preg_replace('/\{\{\s*\$[a-zA-Z_][a-zA-Z0-9_]*\s*\}\}/', '""', $templateContent);
    
    // Final cleanup: remove any remaining malformed Blade expressions
    $templateContent = preg_replace('/\{\{[^}]*\}\}/', '""', $templateContent);
    
    // CRITICAL: Ensure JavaScript always gets the correct wedding card ID
    // This must happen AFTER all Blade processing to ensure it works regardless of processing order
    if (strpos($templateContent, 'window.weddingCardId') !== false) {
        // Replace any existing window.weddingCardId assignment with the correct value
        $templateContent = preg_replace(
            '/window\.weddingCardId\s*=\s*[^;]+;/',
            'window.weddingCardId = ' . $card->id . ';',
            $templateContent
        );
    } else {
        // If no window.weddingCardId exists, add it to the first script tag
        $templateContent = preg_replace(
            '/(<script[^>]*>)/',
            '$1' . "\n        window.weddingCardId = " . $card->id . ";\n",
            $templateContent,
            1
        );
    }
    
    // Add RSVP messages to JavaScript for template use
    if (strpos($templateContent, 'window.rsvpMessages') !== false) {
        // Replace any existing window.rsvpMessages assignment with the correct value
        $templateContent = preg_replace(
            '/window\.rsvpMessages\s*=\s*[^;]+;/',
            'window.rsvpMessages = ' . $rsvpMessagesJson . ';',
            $templateContent
        );
    } else {
        // If no window.rsvpMessages exists, add it to the first script tag
        $templateContent = preg_replace(
            '/(<script[^>]*>)/',
            '$1' . "\n        window.rsvpMessages = " . $rsvpMessagesJson . ";\n",
            $templateContent,
            1
        );
    }
    
    // Add meta tag for mobile responsiveness
    if (strpos($templateContent, '<meta name="viewport"') === false) {
        $templateContent = str_replace('<head>', '<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">', $templateContent);
    }

    // Inject Open Graph tags for link previews if not already present
    if (stripos($templateContent, 'og:title') === false) {
        $ogTags = "\n    <meta property=\"og:title\" content=\"" . htmlspecialchars($ogTitle, ENT_QUOTES) . "\">\n" .
                 "    <meta property=\"og:description\" content=\"" . htmlspecialchars($ogDescription, ENT_QUOTES) . "\">\n" .
                 "    <meta property=\"og:image\" content=\"" . htmlspecialchars($ogImage, ENT_QUOTES) . "\">\n" .
                 "    <meta property=\"og:url\" content=\"" . htmlspecialchars($ogUrl, ENT_QUOTES) . "\">\n" .
                 "    <meta property=\"og:type\" content=\"website\">\n" .
                 "    <meta name=\"twitter:card\" content=\"summary_large_image\">\n";
        $templateContent = str_replace('<head>', '<head>' . "\n" . $ogTags, $templateContent);
    }
    
    echo $templateContent;
} else {
    // Fallback content if no template is found
    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Wedding Invitation</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .error { color: #e74c3c; font-size: 18px; }
    </style>
</head>
<body>
    <div class='error'>
        <h2>Sorry, this wedding invitation is not available.</h2>
        <p>The template could not be loaded.</p>
    </div>
</body>
</html>";
}

?> 