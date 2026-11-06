<?php

class PdfService
{
    public static function generate(string $title, array $lines, string $filename): void
    {
        $directory = STORAGE_PATH . '/documents';
        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new RuntimeException('Impossible de créer le répertoire des documents PDF.');
        }

        $path = $directory . '/' . $filename;
        $lines = array_map(static fn ($value) => trim((string)$value), $lines);
        $content = implode("\n", $lines);
        $pdf = self::buildPdf($title, $content);
        if (file_put_contents($path, $pdf) === false) {
            throw new RuntimeException('Impossible d’enregistrer le document PDF.');
        }
    }

    private static function buildPdf(string $title, string $content): string
    {
        $safeTitle = self::escapePdfText($title);
        $lines = array_values(array_filter(array_map(static fn ($line) => trim((string)$line), preg_split('/\r\n|\r|\n/', $content) ?: []), static fn ($line) => $line !== ''));

        $streamParts = [
            'BT',
            '/F1 18 Tf',
            '50 780 Td',
            '(' . $safeTitle . ') Tj',
            '/F1 11 Tf',
        ];

        $y = 750;
        foreach ($lines as $line) {
            $escapedLine = self::escapePdfText($line);
            $streamParts[] = '1 0 0 1 50 ' . $y . ' Tm';
            $streamParts[] = '(' . $escapedLine . ') Tj';
            $y -= 18;
        }

        $streamParts[] = 'ET';
        $stream = implode("\n", $streamParts);

        $objects = [];
        $objects[] = "<< /Type /Catalog /Pages 2 0 R >>";
        $objects[] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
        $objects[] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>";
        $objects[] = "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream";
        $objects[] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xrefOffset . "\n%%EOF";

        return $pdf;
    }

    private static function escapePdfText(string $value): string
    {
        $converted = iconv('UTF-8', 'Windows-1252//TRANSLIT', $value);
        if ($converted === false) {
            throw new RuntimeException('Impossible d’encoder le texte du document PDF.');
        }
        $value = $converted;
        $value = str_replace('\\', '\\\\', $value);
        $value = str_replace('(', '\\(', $value);
        $value = str_replace(')', '\\)', $value);
        $value = str_replace("\r", ' ', $value);
        $value = str_replace("\n", ' ', $value);
        return preg_replace('/\s+/', ' ', $value) ?? $value;
    }
}
