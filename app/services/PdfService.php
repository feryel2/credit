<?php

class PdfService
{
    public static function generate(string $title, string $content, string $filename): void
    {
        $directory = STORAGE_PATH . '/documents';
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $path = $directory . '/' . $filename;
        $html = '<html><head><meta charset="UTF-8"><title>' . htmlspecialchars($title) . '</title>' .
            '<style>' .
            'body { font-family: Arial, sans-serif; margin: 40px; color: #1f2937; }' .
            'h1 { color: #111827; }' .
            '.box { border: 1px solid #d1d5db; padding: 20px; border-radius: 8px; }' .
            '</style></head><body>' .
            '<div class="box"><h1>' . htmlspecialchars($title) . '</h1>' . $content . '</div></body></html>';

        file_put_contents($path, $html);
    }
}
