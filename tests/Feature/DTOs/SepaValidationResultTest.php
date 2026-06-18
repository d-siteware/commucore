<?php

declare(strict_types=1);

use App\DTOs\SepaValidationResult;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('SepaValidationResult', function (): void {

    it('creates a valid result with no errors', function (): void {
        $result = new SepaValidationResult(valid: true);

        expect($result->valid)->toBeTrue();
        expect($result->errors)->toBeArray();
        expect($result->errors)->toHaveCount(0);
    });

    it('creates an invalid result with errors', function (): void {
        $error = new LibXMLError;
        $error->code = 1234;
        $error->message = 'Element is invalid';
        $error->line = 42;

        $result = new SepaValidationResult(valid: false, errors: [$error]);

        expect($result->valid)->toBeFalse();
        expect($result->errors)->toHaveCount(1);
        expect($result->errors[0])->toBe($error);
    });

    it('generates passed summary for valid result', function (): void {
        $result = new SepaValidationResult(valid: true);

        expect($result->summary())->toBe(__('sepa.validation.passed'));
        expect($result->toFlash())->toBe(__('sepa.validation.passed'));
    });

    it('generates error summary for invalid result', function (): void {
        $error = new LibXMLError;
        $error->code = 1234;
        $error->message = 'Element is invalid';
        $error->line = 42;

        $result = new SepaValidationResult(valid: false, errors: [$error]);

        $summary = $result->summary();
        expect($summary)->toContain(__('sepa.validation.failed', ['count' => 1]));
        expect($summary)->toContain((string) $error->line);
        expect($summary)->toContain(trim($error->message));

        $flash = $result->toFlash();
        expect($flash)->toContain(__('sepa.validation.failed', ['count' => 1]));
        expect($flash)->toContain(trim($error->message));
    });

    it('handles multiple errors', function (): void {
        $error1 = new LibXMLError;
        $error1->message = 'Error 1';
        $error1->line = 10;

        $error2 = new LibXMLError;
        $error2->message = 'Error 2';
        $error2->line = 20;

        $result = new SepaValidationResult(valid: false, errors: [$error1, $error2]);

        expect($result->errors)->toHaveCount(2);
        expect($result->summary())->toContain('Error 1');
        expect($result->summary())->toContain('Error 2');
    });

});
