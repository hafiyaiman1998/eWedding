<?php

namespace App\Services;

class TemplateRenderer
{
    /**
     * Render a hand-rolled Blade-emulation template into HTML.
     *
     * The two historical call sites (admin full preview and user template
     * preview) used near-identical algorithms with a few genuine differences.
     * Those differences are preserved verbatim and selected via $options:
     *
     *  - 'wedding_card' (array|null): when provided, $weddingCard->... tokens
     *     are substituted using this data (admin full preview only).
     *  - 'process_csrf' (bool): handle {{ csrf_token() }} and @csrf (admin only).
     *  - 'combine_ampersand' (bool): handle the "substr & substr" combining and
     *     the extra date()/date+variable expressions (admin only).
     *  - 'for_callback' (bool): run the trailing @for regex-callback pass that
     *     existed only in the user renderer.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $options
     */
    public function render(string $template, array $data, array $options = []): string
    {
        $weddingCard = $options['wedding_card'] ?? null;
        $processCsrf = $options['process_csrf'] ?? false;
        $combineAmpersand = $options['combine_ampersand'] ?? false;
        $forCallback = $options['for_callback'] ?? false;
        $substrBeforeDate = $options['substr_before_date'] ?? false;
        $doubleQuotedFirst = $options['double_quoted_first'] ?? false;

        $htmlContent = $template;

        $htmlContent = $this->processForGallery($htmlContent, $data);

        if (is_array($weddingCard)) {
            $htmlContent = $this->processWeddingCard($htmlContent, $weddingCard);
        }

        $htmlContent = $this->processFunctionExpressions($htmlContent, $data, $substrBeforeDate);

        if ($processCsrf) {
            $htmlContent = $this->processCsrf($htmlContent);
        }

        $htmlContent = $this->processVariables($htmlContent, $data, $doubleQuotedFirst);
        $htmlContent = $this->processConditionalsWithElse($htmlContent, $data);
        $htmlContent = $this->processSimpleConditionals($htmlContent, $data);
        $htmlContent = $this->cleanupConditionals($htmlContent);

        if ($forCallback) {
            $htmlContent = $this->processForCallback($htmlContent, $data);
        }

        if ($combineAmpersand) {
            $htmlContent = $this->processAmpersandCombining($htmlContent, $data);
        } else {
            $htmlContent = $this->processTrailingDates($htmlContent, $data);
        }

        $htmlContent = $this->finalCleanup($htmlContent);

        return $htmlContent;
    }

    /**
     * Process the leading @for gallery block via string replacement.
     *
     * @param  array<string, mixed>  $previewData
     */
    private function processForGallery(string $htmlContent, array $previewData): string
    {
        if (strpos($htmlContent, '@for') === false) {
            return $htmlContent;
        }

        $forPos = strpos($htmlContent, '@for');
        $endforPos = strpos($htmlContent, '@endfor', $forPos);

        if ($endforPos === false) {
            return $htmlContent;
        }

        $forSection = substr($htmlContent, $forPos, $endforPos - $forPos + 7);

        if (preg_match('/@for\s*\(\s*\$(\w+)\s*=\s*(\d+);\s*\$\w+\s*<=\s*(\d+);\s*\$\w+\+\+\s*\)/', $forSection, $matches)) {
            $startValue = (int) $matches[2];
            $endValue = (int) $matches[3];

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
                $galleryContent .= '                <div class="gallery-item fade-in">'."\n";
                $galleryContent .= '                    <img src="'.$photoUrl.'" alt="Gallery Photo '.$i.'">'."\n";
                $galleryContent .= '                </div>'."\n";
            }

            $htmlContent = str_replace($forSection, $galleryContent, $htmlContent);
        }

        return $htmlContent;
    }

    /**
     * Substitute $weddingCard->... tokens (admin full preview only).
     *
     * @param  array<string, mixed>  $weddingCardData
     */
    private function processWeddingCard(string $htmlContent, array $weddingCardData): string
    {
        $htmlContent = preg_replace_callback(
            '/\{\{\s*\$weddingCard->id\s*\?\?\s*[\'"]?([^\}\'"]*)[\'"]*\s*\}\}/',
            function ($matches) use ($weddingCardData) {
                return $weddingCardData['id'];
            },
            $htmlContent
        );

        $htmlContent = preg_replace_callback(
            '/\{\{\s*\$weddingCard->([a-zA-Z_]+)\s*\?\?\s*[\'"]?([^\}\'"]*)[\'"]*\s*\}\}/',
            function ($matches) use ($weddingCardData) {
                $property = $matches[1];
                $fallback = $matches[2] ?? '';

                return $weddingCardData[$property] ?? $fallback;
            },
            $htmlContent
        );

        return preg_replace_callback(
            '/\{\{\s*\$weddingCard->([a-zA-Z_]+)\s*\}\}/',
            function ($matches) use ($weddingCardData) {
                $property = $matches[1];

                return $weddingCardData[$property] ?? '';
            },
            $htmlContent
        );
    }

    /**
     * Handle strtoupper()/substr()/date() function expressions.
     *
     * @param  array<string, mixed>  $previewData
     */
    private function processFunctionExpressions(string $htmlContent, array $previewData, bool $substrBeforeDate): string
    {
        $htmlContent = preg_replace_callback(
            '/\{\{\s*strtoupper\(\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\)\s*\}\}/',
            function ($matches) use ($previewData) {
                $key = $matches[1];
                $fallback = $matches[2];

                return strtoupper($previewData[$key] ?? $fallback);
            },
            $htmlContent
        );

        $substr = function (string $content) use ($previewData): string {
            return preg_replace_callback(
                '/\{\{\s*substr\(\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*,\s*(\d+)\s*,\s*(\d+)\)\s*\}\}/',
                function ($matches) use ($previewData) {
                    $key = $matches[1];
                    $fallback = $matches[2];
                    $start = (int) $matches[3];
                    $length = (int) $matches[4];

                    return substr($previewData[$key] ?? $fallback, $start, $length);
                },
                $content
            );
        };

        $date = function (string $content): string {
            return preg_replace_callback(
                '/\{\{\s*date\("([^"]+)"\)\s*\}\}/',
                function ($matches) {
                    return date($matches[1]);
                },
                $content
            );
        };

        if ($substrBeforeDate) {
            return $date($substr($htmlContent));
        }

        return $substr($date($htmlContent));
    }

    private function processCsrf(string $htmlContent): string
    {
        $htmlContent = preg_replace_callback(
            '/\{\{\s*csrf_token\(\)\s*\}\}/',
            function ($matches) {
                return csrf_token();
            },
            $htmlContent
        );

        return str_replace('@csrf', '<input type="hidden" name="_token" value="'.csrf_token().'">', $htmlContent);
    }

    /**
     * Handle $details[...] variable substitution (with and without fallbacks).
     *
     * @param  array<string, mixed>  $previewData
     */
    private function processVariables(string $htmlContent, array $previewData, bool $doubleQuotedFirst): string
    {
        $htmlContent = preg_replace_callback(
            '/\{\{\s*\$details\[\'([^\']+)\'\]\s*\?\?\s*\$details\[\'([^\']+)\'\]\s*\?\?\s*\'([^\']*)\'\s*\}\}/',
            function ($matches) use ($previewData) {
                $key1 = $matches[1];
                $key2 = $matches[2];
                $fallback = $matches[3];

                return $previewData[$key1] ?? $previewData[$key2] ?? $fallback;
            },
            $htmlContent
        );

        $htmlContent = preg_replace_callback(
            '/\{\{\s*\$details\["([^"]+)"\]\s*\?\?\s*\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*\}\}/',
            function ($matches) use ($previewData) {
                $key1 = $matches[1];
                $key2 = $matches[2];
                $fallback = $matches[3];

                return $previewData[$key1] ?? $previewData[$key2] ?? $fallback;
            },
            $htmlContent
        );

        $doubleFallback = function (string $content) use ($previewData): string {
            return preg_replace_callback(
                '/\{\{\s*\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*\}\}/',
                function ($matches) use ($previewData) {
                    return $previewData[$matches[1]] ?? $matches[2];
                },
                $content
            );
        };

        $singleFallback = function (string $content) use ($previewData): string {
            return preg_replace_callback(
                '/\{\{\s*\$details\[\'([^\']+)\'\]\s*\?\?\s*\'([^\']*)\'\s*\}\}/',
                function ($matches) use ($previewData) {
                    return $previewData[$matches[1]] ?? $matches[2];
                },
                $content
            );
        };

        $doubleSimple = function (string $content) use ($previewData): string {
            return preg_replace_callback(
                '/\{\{\s*\$details\["([^"]+)"\]\s*\}\}/',
                function ($matches) use ($previewData) {
                    return $previewData[$matches[1]] ?? '';
                },
                $content
            );
        };

        $singleSimple = function (string $content) use ($previewData): string {
            return preg_replace_callback(
                '/\{\{\s*\$details\[\'([^\']+)\'\]\s*\}\}/',
                function ($matches) use ($previewData) {
                    return $previewData[$matches[1]] ?? '';
                },
                $content
            );
        };

        if ($doubleQuotedFirst) {
            $htmlContent = $singleFallback($doubleFallback($htmlContent));

            return $singleSimple($doubleSimple($htmlContent));
        }

        $htmlContent = $doubleFallback($singleFallback($htmlContent));

        return $doubleSimple($singleSimple($htmlContent));
    }

    /**
     * Handle @if/@else/@endif blocks (processed before simple @if blocks).
     *
     * @param  array<string, mixed>  $previewData
     */
    private function processConditionalsWithElse(string $htmlContent, array $previewData): string
    {
        $htmlContent = preg_replace_callback(
            '/@if\(\$details\["([^"]+)"\]\s*\?\?\s*false\)(.*?)@else(.*?)@endif/s',
            function ($matches) use ($previewData) {
                return ! empty($previewData[$matches[1]]) ? $matches[2] : $matches[3];
            },
            $htmlContent
        );

        $htmlContent = preg_replace_callback(
            '/@if\(\$details\[\'([^\']+)\'\]\s*\?\?\s*false\)(.*?)@else(.*?)@endif/s',
            function ($matches) use ($previewData) {
                return ! empty($previewData[$matches[1]]) ? $matches[2] : $matches[3];
            },
            $htmlContent
        );

        $htmlContent = preg_replace_callback(
            '/@if\(isset\(\$details\["([^"]+)"\]\)\)(.*?)@else(.*?)@endif/s',
            function ($matches) use ($previewData) {
                return (isset($previewData[$matches[1]]) && ! empty($previewData[$matches[1]])) ? $matches[2] : $matches[3];
            },
            $htmlContent
        );

        $htmlContent = preg_replace_callback(
            '/@if\(isset\(\$details\[\'([^\']+)\'\]\)\)(.*?)@else(.*?)@endif/s',
            function ($matches) use ($previewData) {
                return (isset($previewData[$matches[1]]) && ! empty($previewData[$matches[1]])) ? $matches[2] : $matches[3];
            },
            $htmlContent
        );

        $htmlContent = preg_replace_callback(
            '/@if\(\$details\["([^"]+)"\]\)(.*?)@else(.*?)@endif/s',
            function ($matches) use ($previewData) {
                return ! empty($previewData[$matches[1]]) ? $matches[2] : $matches[3];
            },
            $htmlContent
        );

        return preg_replace_callback(
            '/@if\(\$details\[\'([^\']+)\'\]\)(.*?)@else(.*?)@endif/s',
            function ($matches) use ($previewData) {
                return ! empty($previewData[$matches[1]]) ? $matches[2] : $matches[3];
            },
            $htmlContent
        );
    }

    /**
     * Handle remaining simple @if blocks (OR, ?? false, isset, plain).
     *
     * @param  array<string, mixed>  $previewData
     */
    private function processSimpleConditionals(string $htmlContent, array $previewData): string
    {
        $htmlContent = preg_replace_callback(
            '/@if\(\$details\["([^"]+)"\]\s*\|\|\s*\$details\["([^"]+)"\]\)(.*?)@endif/s',
            function ($matches) use ($previewData) {
                return (! empty($previewData[$matches[1]]) || ! empty($previewData[$matches[2]])) ? $matches[3] : '';
            },
            $htmlContent
        );

        $htmlContent = preg_replace_callback(
            '/@if\(\$details\["([^"]+)"\]\s*\?\?\s*false\)(.*?)@endif/s',
            function ($matches) use ($previewData) {
                return ! empty($previewData[$matches[1]]) ? $matches[2] : '';
            },
            $htmlContent
        );

        $htmlContent = preg_replace_callback(
            '/@if\(\$details\[\'([^\']+)\'\]\s*\?\?\s*false\)(.*?)@endif/s',
            function ($matches) use ($previewData) {
                return ! empty($previewData[$matches[1]]) ? $matches[2] : '';
            },
            $htmlContent
        );

        $htmlContent = preg_replace_callback(
            '/@if\(isset\(\$details\["([^"]+)"\]\)\)(.*?)@endif/s',
            function ($matches) use ($previewData) {
                return isset($previewData[$matches[1]]) && ! empty($previewData[$matches[1]]) ? $matches[2] : '';
            },
            $htmlContent
        );

        $htmlContent = preg_replace_callback(
            '/@if\(isset\(\$details\[\'([^\']+)\'\]\)\)(.*?)@endif/s',
            function ($matches) use ($previewData) {
                return isset($previewData[$matches[1]]) && ! empty($previewData[$matches[1]]) ? $matches[2] : '';
            },
            $htmlContent
        );

        $htmlContent = preg_replace_callback(
            '/@if\(\$details\["([^"]+)"\]\)(.*?)@else(.*?)@endif/s',
            function ($matches) use ($previewData) {
                return ! empty($previewData[$matches[1]]) ? $matches[2] : $matches[3];
            },
            $htmlContent
        );

        return preg_replace_callback(
            '/@if\(\$details\[\'([^\']+)\'\]\)(.*?)@else(.*?)@endif/s',
            function ($matches) use ($previewData) {
                return ! empty($previewData[$matches[1]]) ? $matches[2] : $matches[3];
            },
            $htmlContent
        );
    }

    private function cleanupConditionals(string $htmlContent): string
    {
        $htmlContent = str_replace('@endif', '', $htmlContent);
        $htmlContent = preg_replace('/@if\([^)]+\)/', '', $htmlContent);

        return str_replace('@else', '', $htmlContent);
    }

    /**
     * Trailing @for regex-callback pass (user renderer only).
     *
     * @param  array<string, mixed>  $previewData
     */
    private function processForCallback(string $htmlContent, array $previewData): string
    {
        return preg_replace_callback(
            '/@for\(\$i\s*=\s*(\d+);\s*\$i\s*<=\s*(\d+);\s*\$i\+\+\)(.*?)@endfor/s',
            function ($matches) use ($previewData) {
                $start = (int) $matches[1];
                $end = (int) $matches[2];
                $content = $matches[3];
                $result = '';

                for ($i = $start; $i <= $end; $i++) {
                    $iterationContent = $content;
                    $iterationContent = str_replace('$i', $i, $iterationContent);

                    $iterationContent = preg_replace_callback(
                        '/\{\{\s*\$details\["gallery_photo_"\s*\.\s*\$i\]\s*\?\?\s*false\s*\}\}/',
                        function ($m) use ($previewData, $i) {
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
    }

    /**
     * Handle the "substr & substr" combining plus the date()/date+variable
     * expressions (admin renderer only).
     *
     * @param  array<string, mixed>  $previewData
     */
    private function processAmpersandCombining(string $htmlContent, array $previewData): string
    {
        $htmlContent = preg_replace_callback(
            '/\{\{\s*substr\(\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*,\s*(\d+)\s*,\s*(\d+)\)\s*\}\}\s*&\s*\{\{\s*substr\(\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*,\s*(\d+)\s*,\s*(\d+)\)\s*\}\}/',
            function ($matches) use ($previewData) {
                $value1 = substr($previewData[$matches[1]] ?? $matches[2], (int) $matches[3], (int) $matches[4]);
                $value2 = substr($previewData[$matches[5]] ?? $matches[6], (int) $matches[7], (int) $matches[8]);

                return $value1.' & '.$value2;
            },
            $htmlContent
        );

        $htmlContent = preg_replace_callback(
            '/\{\{\s*date\(\'([^\']+)\'\)\s*\}\}/',
            function ($matches) {
                return date($matches[1]);
            },
            $htmlContent
        );

        $htmlContent = preg_replace_callback(
            '/\{\{\s*date\("([^"]+)"\)\s*\}\}/',
            function ($matches) {
                return date($matches[1]);
            },
            $htmlContent
        );

        return preg_replace_callback(
            '/\{\{\s*date\(\'([^\']+)\'\)\s*\}\}\s*\{\{\s*\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*\}\}/',
            function ($matches) use ($previewData) {
                return date($matches[1]).' '.($previewData[$matches[2]] ?? $matches[3]);
            },
            $htmlContent
        );
    }

    /**
     * Handle trailing {{ date('...') }} expressions plus the date+variable
     * combination present in both renderers' tail sections.
     *
     * @param  array<string, mixed>  $previewData
     */
    private function processTrailingDates(string $htmlContent, array $previewData): string
    {
        $htmlContent = preg_replace_callback(
            '/\{\{\s*date\(\'([^\']+)\'\)\s*\}\}/',
            function ($matches) {
                return date($matches[1]);
            },
            $htmlContent
        );

        return preg_replace_callback(
            '/\{\{\s*date\(\'([^\']+)\'\)\s*\}\}\s*\{\{\s*\$details\["([^"]+)"\]\s*\?\?\s*"([^"]*)"\s*\}\}/',
            function ($matches) use ($previewData) {
                return date($matches[1]).' '.($previewData[$matches[2]] ?? $matches[3]);
            },
            $htmlContent
        );
    }

    private function finalCleanup(string $htmlContent): string
    {
        $htmlContent = str_replace('<?php', '', $htmlContent);
        $htmlContent = str_replace('?>', '', $htmlContent);

        return preg_replace('/\{\{\s*\}\}/', '', $htmlContent);
    }
}
