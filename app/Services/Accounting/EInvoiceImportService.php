<?php

declare(strict_types=1);

namespace App\Services\Accounting;

use App\DTOs\Accounting\EInvoiceData;
use App\Enums\TransactionType;
use App\Exceptions\EInvoiceImportException;
use App\Helpers\MoneyHelper;
use DateTimeInterface;
use horstoeko\invoicesuite\InvoiceSuiteDocumentReader;
use horstoeko\invoicesuite\InvoiceSuitePdfDocumentReader;
use horstoeko\invoicesuite\InvoiceSuiteSettings;
use horstoeko\invoicesuite\validators\InvoiceSuiteXsdDocumentValidator;
use Throwable;

final class EInvoiceImportService
{
    private const VAT_RATES = [0, 7, 19];

    public function parse(string $content, string $extension, string $originalName): EInvoiceData
    {
        if (! in_array(strtolower($extension), ['pdf', 'xml'], true)) {
            throw new EInvoiceImportException('unsupported');
        }

        try {
            InvoiceSuiteSettings::setDiscoveryNamespaces([
                'horstoeko\\invoicesuite\\documents\\providers',
            ]);

            if (strtolower($extension) === 'pdf') {
                $pdfReader = InvoiceSuitePdfDocumentReader::createFromContent($content);
                $invoiceAttachment = $pdfReader->getInvoiceDocumentAttachment();

                if ($invoiceAttachment === null) {
                    throw new EInvoiceImportException('unsupported');
                }

                $xml = $invoiceAttachment->getAttachmentContent();
                $reader = $pdfReader->getDocumentReader();
            } else {
                $xml = $content;
                $reader = InvoiceSuiteDocumentReader::createFromContent($xml);
            }

            $provider = $reader->getCurrentDocumentFormatProvider()?->getUniqueId() ?? '';
            if (! str_starts_with($provider, 'xrechnung') && ! str_starts_with($provider, 'zffx')) {
                throw new EInvoiceImportException('unsupported');
            }

            $validator = InvoiceSuiteXsdDocumentValidator::createFromContent($xml)->validate();
            if ($validator->hasErrorOrInternalErrorMessagesInMessageBag()) {
                throw new EInvoiceImportException('invalid');
            }

            $reader->getDocumentNo($number);
            $reader->getDocumentType($documentType);
            $reader->getDocumentDate($date);
            $reader->getDocumentCurrency($currency);
            $reader->getDocumentSellerName($sellerName);
            $reader->getDocumentDescription($description);

            if (strtoupper((string) $currency) !== 'EUR') {
                throw new EInvoiceImportException('currency');
            }

            if (! $date instanceof DateTimeInterface) {
                throw new EInvoiceImportException('incomplete');
            }

            $rates = [];
            if ($reader->firstDocumentTax()) {
                do {
                    $reader->getDocumentTax(
                        $category,
                        $taxType,
                        $basisAmount,
                        $taxAmount,
                        $rate,
                        $exemptionReason,
                        $exemptionReasonCode,
                        $taxDueDate,
                        $taxDueCode,
                    );

                    $rates[] = (int) round((float) $rate);
                } while ($reader->nextDocumentTax());
            }

            $rates = array_values(array_unique($rates));
            if (count($rates) > 1 || ! in_array($rates[0] ?? 0, self::VAT_RATES, true)) {
                throw new EInvoiceImportException('tax_rates');
            }

            $reader->getDocumentSummation(
                $netAmount,
                $chargeAmount,
                $discountAmount,
                $taxBasisAmount,
                $taxAmount,
                $accountingTaxAmount,
                $grossAmount,
                $dueAmount,
                $prepaidAmount,
                $roundingAmount,
            );

            $grossCents = (int) round((float) $grossAmount * 100);
            $taxCents = (int) round((float) $taxAmount * 100);
            $netCents = $grossCents - $taxCents;

            if ($grossCents <= 0 || $netCents < 0) {
                throw new EInvoiceImportException('incomplete');
            }

            $reference = trim((string) $number);
            $reference = $reference !== '' ? $reference : null;
            $sellerName = trim((string) $sellerName);
            $label = $sellerName !== ''
                ? mb_substr($sellerName, 0, 255)
                : mb_substr($reference ?? pathinfo($originalName, PATHINFO_FILENAME), 0, 255);
            $description = trim((string) $description);
            $description = $description !== '' ? $description : null;
            $isCreditNote = $documentType === '381' || str_contains($provider, 'creditnote');

            return new EInvoiceData(
                date: $date->format('Y-m-d'),
                label: $label,
                reference: $reference,
                description: $description,
                amountNet: MoneyHelper::formatCents($netCents, withSymbol: false),
                amountGross: MoneyHelper::formatCents($grossCents, withSymbol: false),
                tax: MoneyHelper::formatCents($taxCents, withSymbol: false),
                vat: $rates[0] ?? 0,
                type: $isCreditNote ? TransactionType::Deposit : TransactionType::Withdrawal,
                format: $provider,
            );
        } catch (EInvoiceImportException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw new EInvoiceImportException('invalid', $exception);
        }
    }
}
