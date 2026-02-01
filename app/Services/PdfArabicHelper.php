<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf as PDF;

/**
 * Central PDF generation with correct Arabic/RTL display for the whole system.
 * DomPDF does not apply the Unicode Bidirectional Algorithm (UAX #9),
 * so RTL/Arabic text is drawn left-to-right and appears reversed. This helper
 * reverses entire text nodes that contain Arabic so that when DomPDF renders LTR,
 * the visual result is correct.
 *
 * Use loadViewAsPdf() everywhere PDFs are generated so Arabic displays correctly.
 */
class PdfArabicHelper
{
    /**
     * Load a Blade view as PDF with Arabic fix and UTF-8 applied system-wide.
     * Ensures storage/fonts exists, renders the view, reverses Arabic in HTML, then loads with DomPDF.
     *
     * @param  string  $view  Blade view name (e.g. 'supplier.invoices.pdf')
     * @param  array<string, mixed>  $data  View data
     * @return \Barryvdh\DomPDF\PDF
     */
    public static function loadViewAsPdf(string $view, array $data = []): \Barryvdh\DomPDF\PDF
    {
        self::ensureFontDirExists();
        $html = view($view, $data)->render();
        $html = self::reverseArabicInHtml($html);

        return PDF::loadHTML($html, 'UTF-8');
    }

    /**
     * Ensure DomPDF font directory exists so font cache can be written.
     */
    public static function ensureFontDirExists(): void
    {
        $fontDir = storage_path('fonts');
        if (! is_dir($fontDir)) {
            mkdir($fontDir, 0755, true);
        }
    }

    /**
     * Reverse entire text nodes that contain Arabic in rendered HTML so DomPDF displays them correctly.
     * Only text between tags that contains Arabic is reversed as a whole (UTF-8 safe); tag structure is preserved.
     */
    public static function reverseArabicInHtml(string $html): string
    {
        return preg_replace_callback('/>([^<]+)</us', function (array $m): string {
            $text = $m[1];
            if (! preg_match('/[\x{0600}-\x{06FF}]/u', $text)) {
                return $m[0];
            }
            $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);

            return '>'.implode('', array_reverse($chars)).'<';
        }, $html);
    }
}
