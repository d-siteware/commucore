<?php

declare(strict_types=1);

use App\DTOs\SepaValidationResult;
use App\Services\Sepa\SepaXmlValidator;
use Digitick\Sepa\PaymentInformation;
use Digitick\Sepa\TransferFile\Factory\TransferFileFacadeFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function xmlValidator(): SepaXmlValidator
{
    return app(SepaXmlValidator::class);
}

function generateMinimalValidXml(string $format = 'pain.008.001.09'): string
{
    $facade = TransferFileFacadeFactory::createDirectDebit(
        'TEST-'.now()->format('YmdHis'),
        'Testbank',
        $format,
    );

    $facade->addPaymentInfo('pmt-test-001', [
        'id' => 'pmt-test-001',
        'dueDate' => new DateTimeImmutable('+5 weekdays'),
        'creditorName' => 'Testbank',
        'creditorAccountIBAN' => 'DE89370400440532013000',
        'creditorAgentBIC' => 'COBADEFFXXX',
        'seqType' => PaymentInformation::S_FIRST,
        'creditorId' => 'DE00ZZZ00000000000',
        'localInstrumentCode' => 'CORE',
    ]);

    $facade->addTransfer('pmt-test-001', [
        'amount' => 6000,
        'debtorIban' => 'DE02700100800029868074',
        'debtorBic' => 'PBNKDEFF',
        'debtorName' => 'Max Mustermann',
        'debtorMandate' => 'MANDAT-001',
        'debtorMandateSignDate' => '01.01.2026',
        'remittanceInformation' => 'Test Lastschrift',
        'endToEndId' => 'E2E-TEST-001',
    ]);

    return $facade->asXML();
}

describe('validate', function (): void {

    it('passes valid pain.008.001.09 XML', function (): void {
        $xml = generateMinimalValidXml('pain.008.001.09');
        $result = xmlValidator()->validate($xml, 'pain.008.001.09');

        expect($result)->toBeInstanceOf(SepaValidationResult::class);
        expect($result->valid)->toBeTrue();
        expect($result->errors)->toBeArray();
        expect($result->errors)->toHaveCount(0);
    });

    it('fails on invalid XML', function (): void {
        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<Document xmlns="urn:iso:std:iso:20022:tech:xsd:pain.008.001.09">
  <InvalidTag />
</Document>
XML;
        $result = xmlValidator()->validate($xml, 'pain.008.001.09');

        expect($result->valid)->toBeFalse();
        expect($result->errors)->not->toBeEmpty();
    });

    it('fails on empty string', function (): void {
        $result = xmlValidator()->validate('', 'pain.008.001.09');

        expect($result->valid)->toBeFalse();
    });

    it('fails on malformed XML', function (): void {
        $result = xmlValidator()->validate('not xml at all', 'pain.008.001.09');

        expect($result->valid)->toBeFalse();
    });

    it('returns passed summary for valid result', function (): void {
        $xml = generateMinimalValidXml('pain.008.001.09');
        $result = xmlValidator()->validate($xml, 'pain.008.001.09');

        expect($result->summary())->toBe(__('sepa.validation.passed'));
        expect($result->toFlash())->toBe(__('sepa.validation.passed'));
    });

    it('returns error details in summary for invalid result', function (): void {
        $result = xmlValidator()->validate('<InvalidTag />', 'pain.008.001.09');

        $summary = $result->summary();
        expect($summary)->toContain(__('sepa.validation.failed', ['count' => count($result->errors)]));
    });

    it('validates pain.008.003.01 format (CH)', function (): void {
        $xml = generateMinimalValidXml('pain.008.003.02');
        $result = xmlValidator()->validate($xml, 'pain.008.003.01');

        expect($result->valid)->toBeTrue();
    });

    it('validates pain.008.001.02 format (DE-alt)', function (): void {
        $xml = generateMinimalValidXml('pain.008.001.02');
        $result = xmlValidator()->validate($xml, 'pain.008.001.02');

        expect($result->valid)->toBeTrue();
    });

    it('returns invalid for unsupported format', function (): void {
        $xml = generateMinimalValidXml('pain.008.001.09');
        $result = xmlValidator()->validate($xml, 'pain.008.999.99');

        expect($result->valid)->toBeFalse();
    });

});
