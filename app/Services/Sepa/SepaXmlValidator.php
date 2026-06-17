<?php

declare(strict_types=1);

namespace App\Services\Sepa;

use App\DTOs\SepaValidationResult;
use DOMDocument;

final class SepaXmlValidator
{
    private const SCHEMA_MAP = [
        'pain.008.001.02' => 'pain.008.001.02.xsd',
        'pain.008.001.09' => 'pain.008.001.09.xsd',
        'pain.008.003.01' => 'pain.008.003.02.xsd',
        'pain.008.003.02' => 'pain.008.003.02.xsd',
    ];

    public function validate(string $xml, string $painFormat = 'pain.008.001.09'): SepaValidationResult
    {
        $xsdPath = $this->resolveSchemaPath($painFormat);

        if ($xsdPath === null || !file_exists($xsdPath)) {
            return new SepaValidationResult(
                valid: false,
                errors: [self::libxmlError(
                    line: 0,
                    message: sprintf('Schema not found for format: %s (looked at %s)', $painFormat, $xsdPath),
                )],
            );
        }

        $dom = new DOMDocument();
        $dom->loadXML($xml);

        $previous = libxml_use_internal_errors(true);

        $valid = $dom->schemaValidate($xsdPath);

        $errors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return new SepaValidationResult(
            valid: $valid,
            errors: $errors,
        );
    }

    private function resolveSchemaPath(string $painFormat): ?string
    {
        $schemaFile = self::SCHEMA_MAP[$painFormat] ?? null;

        if ($schemaFile === null) {
            return null;
        }

        // Vendor library schemas are the primary source; storage_path can override manually
        $vendorSub = str_contains($painFormat, '003') ? '008/003' : '008/001';

        $paths = [
            base_path('vendor/digitick/sepa-xml/doc/ISO20022/pain/'.$vendorSub.'/'.$schemaFile),
            storage_path('app/sepa/schemas/'.$schemaFile),
        ];

        foreach ($paths as $path) {
            if (file_exists($path) && filesize($path) > 100) {
                return $path;
            }
        }

        return null;
    }

    private static function libxmlError(int $line, string $message): \LibXMLError
    {
        $error = new \LibXMLError();
        $error->line = $line;
        $error->message = $message;

        return $error;
    }
}
