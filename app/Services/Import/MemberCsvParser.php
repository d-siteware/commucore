<?php

declare(strict_types=1);

namespace App\Services\Import;

use Illuminate\Http\UploadedFile;

/**
 * Parses a CSV file into headers and rows.
 *
 * Supports semicolon and comma as delimiters (auto-detected).
 * UTF-8 BOM is stripped automatically.
 */
final class MemberCsvParser
{
    private const SUPPORTED_DELIMITERS = [';', ','];

    private const PREVIEW_ROWS = 10;

    /**
     * @return array{
     *     headers: string[],
     *     rows: array<int, array<string, string>>,
     *     all_rows: array<int, array<string, string>>,
     *     total_rows: int,
     *     delimiter: string,
     * }
     */
    public static function parse(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        $content = file_get_contents($path);

        if ($content === false) {
            throw new \RuntimeException('Could not read uploaded CSV file.');
        }

        // Strip UTF-8 BOM if present
        $content = ltrim($content, "\xEF\xBB\xBF");

        $delimiter = self::detectDelimiter($content);
        $lines = self::toLines($content);

        if ($lines === []) {
            throw new \RuntimeException('CSV file is empty.');
        }

        $headers = self::parseHeader(array_shift($lines), $delimiter);

        if ($headers === []) {
            throw new \RuntimeException('CSV file has no headers.');
        }

        $allRows = self::parseRows($lines, $headers, $delimiter);

        return [
            'headers' => $headers,
            'rows' => array_slice($allRows, 0, self::PREVIEW_ROWS),
            'total_rows' => count($allRows),
            'delimiter' => $delimiter,
            'all_rows' => $allRows, // Alle Zeilen für den Import
        ];
    }

    /**
     * Detect delimiter by counting occurrences in first line.
     */
    private static function detectDelimiter(string $content): string
    {
        $firstLine = strtok($content, "\n");

        if ($firstLine === false) {
            return ';';
        }

        $counts = [];
        foreach (self::SUPPORTED_DELIMITERS as $delimiter) {
            $counts[$delimiter] = substr_count($firstLine, $delimiter);
        }

        arsort($counts);

        /** @var string */
        return array_key_first($counts);
    }

    /**
     * @return string[]
     */
    private static function toLines(string $content): array
    {
        return array_filter(
            explode("\n", str_replace("\r\n", "\n", $content)),
            static fn (string $line): bool => trim($line) !== '',
        );
    }

    /**
     * @return string[]
     */
    private static function parseHeader(string $line, string $delimiter): array
    {
        $headers = str_getcsv($line, $delimiter);

        return array_map(
            static fn (string $h): string => trim($h),
            $headers,
        );
    }

    /**
     * @param  string[]  $lines
     * @param  string[]  $headers
     * @return array<int, array<string, string>>
     */
    private static function parseRows(array $lines, array $headers, string $delimiter): array
    {
        $rows = [];

        foreach (array_values($lines) as $i => $line) {
            $values = str_getcsv($line, $delimiter);

            // Zeilen mit falscher Spaltenanzahl überspringen
            if (count($values) !== count($headers)) {
                continue;
            }

            /** @var array<string, string> $row */
            $row = array_combine($headers, array_map(
                static fn (string $v): string => trim($v),
                $values,
            ));

            $rows[$i] = $row;
        }

        return $rows;
    }
}
