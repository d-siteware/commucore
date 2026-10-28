<?php

declare(strict_types=1);

use App\Enums\TransactionType;
use App\Exceptions\EInvoiceImportException;
use App\Helpers\MoneyHelper;
use App\Livewire\Accounting\Transaction\Create\Form;
use App\Models\Accounting\Account;
use App\Models\Accounting\Transaction;
use App\Models\Document;
use App\Models\User;
use App\Services\Accounting\EInvoiceImportService;
use horstoeko\invoicesuite\InvoiceSuiteDocumentBuilder;
use horstoeko\invoicesuite\InvoiceSuitePdfDocumentBuilder;
use horstoeko\invoicesuite\InvoiceSuiteSettings;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

function eInvoiceXmlFixture(
    string $provider = 'xrechnungcii',
    string $documentType = '380',
    bool $mixedRates = false,
): string {
    InvoiceSuiteSettings::setDiscoveryNamespaces([
        'horstoeko\\invoicesuite\\documents\\providers',
    ]);

    $secondNet = $mixedRates ? 100.00 : 0.00;
    $secondTax = $mixedRates ? 7.00 : 0.00;
    $net = 100.00 + $secondNet;
    $tax = 19.00 + $secondTax;
    $gross = $net + $tax;

    $builder = InvoiceSuiteDocumentBuilder::createByProviderUniqueId($provider);
    $builder->setDocumentNo('INV-2026-001');
    $builder->setDocumentType($documentType);
    $builder->setDocumentDate(new DateTime('2026-10-28'));
    $builder->setDocumentCurrency('EUR');
    $builder->setDocumentSellerId('SUPPLIER-1');
    $builder->setDocumentSellerName('Test Supplier GmbH');
    $builder->setDocumentSellerAddress(
        newAddressLine1: 'Seller Street 1',
        newPostcode: '10115',
        newCity: 'Berlin',
        newCountryId: 'DE',
    );
    $builder->setDocumentBuyerId('BUYER-1');
    $builder->setDocumentBuyerName('Test Association e.V.');
    $builder->setDocumentBuyerAddress(
        newAddressLine1: 'Buyer Street 1',
        newPostcode: '10117',
        newCity: 'Berlin',
        newCountryId: 'DE',
    );
    $builder->setDocumentBuyerReference('04011000-12345-67');
    $builder->addDocumentSellerTaxRegistration('VA', 'DE123456789');
    $builder->addDocumentTax(
        newTaxCategory: 'S',
        newTaxType: 'VAT',
        newBasisAmount: 100.00,
        newTaxAmount: 19.00,
        newTaxPercent: 19.00,
    );

    if ($mixedRates) {
        $builder->addDocumentTax(
            newTaxCategory: 'S',
            newTaxType: 'VAT',
            newBasisAmount: 100.00,
            newTaxAmount: 7.00,
            newTaxPercent: 7.00,
        );
    }

    $builder->setDocumentSummation(
        newNetAmount: $net,
        newChargeTotalAmount: 0.00,
        newDiscountTotalAmount: 0.00,
        newTaxBasisAmount: $net,
        newTaxTotalAmount: $tax,
        newGrossAmount: $gross,
        newDueAmount: $gross,
        newPrepaidAmount: 0.00,
    );

    foreach ([19.00, ...($mixedRates ? [7.00] : [])] as $index => $rate) {
        $builder->addDocumentPosition((string) ($index + 1));
        $builder->setDocumentPositionProductDetails(newProductName: 'Service');
        $builder->setDocumentPositionGrossPrice(100.00);
        $builder->setDocumentPositionNetPrice(100.00);
        $builder->setDocumentPositionQuantities(1.00, 'C62');
        $builder->setDocumentPositionTax(
            newTaxCategory: 'S',
            newTaxType: 'VAT',
            newTaxPercent: $rate,
        );
        $builder->setDocumentPositionSummation(100.00);
    }

    return $builder->getContent();
}

function eInvoicePdfFixture(): string
{
    $xml = eInvoiceXmlFixture('zffxcomfort');
    $pdf = new TCPDF;
    $pdf->AddPage();
    $pdf->Write(0, 'Test invoice');

    return InvoiceSuitePdfDocumentBuilder::createFromDocumentContentAndPdfContent(
        $xml,
        $pdf->Output('', 'S'),
    )->generatePdfDocumentAndGetContent();
}

it('reads and validates XRechnung CII XML into transaction values', function (): void {
    $data = app(EInvoiceImportService::class)->parse(eInvoiceXmlFixture(), 'xml', 'invoice.xml');

    expect($data->format)->toBe('xrechnungcii')
        ->and($data->date)->toBe('2026-10-28')
        ->and($data->label)->toBe('Test Supplier GmbH')
        ->and($data->reference)->toBe('INV-2026-001')
        ->and(MoneyHelper::toCents($data->amountNet))->toBe(10000)
        ->and(MoneyHelper::toCents($data->tax))->toBe(1900)
        ->and(MoneyHelper::toCents($data->amountGross))->toBe(11900)
        ->and($data->vat)->toBe(19)
        ->and($data->type)->toBe(TransactionType::Withdrawal);
});

it('reads UBL XRechnung XML', function (): void {
    $data = app(EInvoiceImportService::class)->parse(
        eInvoiceXmlFixture('xrechnungublinvoice'),
        'xml',
        'invoice.xml',
    );

    expect($data->format)->toBe('xrechnungublinvoice')
        ->and($data->reference)->toBe('INV-2026-001')
        ->and(MoneyHelper::toCents($data->amountGross))->toBe(11900);
});

it('maps an electronic credit note to a deposit', function (): void {
    $data = app(EInvoiceImportService::class)->parse(
        eInvoiceXmlFixture(documentType: '381'),
        'xml',
        'credit-note.xml',
    );

    expect($data->type)->toBe(TransactionType::Deposit);
});

it('extracts and validates the embedded XML from a ZUGFeRD PDF', function (): void {
    $data = app(EInvoiceImportService::class)->parse(eInvoicePdfFixture(), 'pdf', 'invoice.pdf');

    expect($data->format)->toBe('zffxcomfort')
        ->and($data->reference)->toBe('INV-2026-001')
        ->and(MoneyHelper::toCents($data->amountGross))->toBe(11900);
});

it('does not flatten invoices with multiple VAT rates into one transaction rate', function (): void {
    expect(fn () => app(EInvoiceImportService::class)->parse(
        eInvoiceXmlFixture(mixedRates: true),
        'xml',
        'invoice.xml',
    ))->toThrow(EInvoiceImportException::class, 'tax_rates');
});

it('prefills the booking wizard and marks the XML as an invoice document', function (): void {
    $user = User::factory()->create(['is_admin' => true]);
    $this->actingAs($user);
    Storage::fake('local');
    $file = UploadedFile::fake()->createWithContent('invoice.xml', eInvoiceXmlFixture());
    $account = Account::factory()->create();

    Livewire::test(Form::class, ['wizard' => true])
        ->set('documentFiles', [$file])
        ->call('importInvoiceData', 0)
        ->assertHasNoErrors()
        ->assertSet('documentCategory', 'invoice')
        ->assertSet('form.date', '2026-10-28')
        ->assertSet('form.label', 'Test Supplier GmbH')
        ->assertSet('form.reference', 'INV-2026-001')
        ->assertSet('form.vat', 19)
        ->assertSet('form.type', TransactionType::Withdrawal)
        ->call('nextStep')
        ->assertSet('step', 2)
        ->call('nextStep')
        ->assertSet('step', 3)
        ->set('form.account_id', $account->id)
        ->call('nextStep')
        ->assertSet('step', 4)
        ->call('submitTransaction')
        ->assertHasNoErrors();

    $transaction = Transaction::where('reference', 'INV-2026-001')->firstOrFail();
    $document = Document::where('original_name', 'invoice.xml')->firstOrFail();

    expect($transaction->amount_net)->toBe(10000)
        ->and($transaction->amount_gross)->toBe(11900)
        ->and($document->documentable->is($transaction))->toBeTrue()
        ->and(Storage::disk('local')->exists($document->path))->toBeTrue();
});
