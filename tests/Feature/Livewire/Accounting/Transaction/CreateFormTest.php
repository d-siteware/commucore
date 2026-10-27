<?php

declare(strict_types=1);

use App\Enums\BookingAccountArea;
use App\Livewire\Accounting\Transaction\Create\Form;
use App\Livewire\Accounting\Transaction\Create\Page as CreatePage;
use App\Models\Accounting\Account;
use App\Models\Accounting\BookingAccount;
use App\Models\Accounting\Transaction;
use App\Models\Document;
use App\Models\Membership\Member;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function (): void {
    $user = User::factory()->create(['is_admin' => true]);
    Member::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);
});

test('a withdrawal transaction can be created', function (): void {
    $account = Account::factory()->create();

    Livewire::test(Form::class)
        ->set('form.label', 'Test Withdrawal')
        ->set('form.date', now()->format('Y-m-d'))
        ->set('form.amount_net', '100.00')
        ->set('form.vat', 19)
        ->set('form.amount_gross', '119.00')
        ->set('form.account_id', $account->id)
        ->set('form.type', 'Withdrawal')
        ->set('form.status', 'submitted')
        ->call('submitTransaction')
        ->assertHasNoErrors();

    $transaction = Transaction::where('label', 'Test Withdrawal')->first();
    expect($transaction)->not->toBeNull()
        ->and($transaction->type->value)->toBe('Withdrawal')
        ->and($transaction->amount_gross)->toBe(11900);
});

test('a deposit transaction can be created', function (): void {
    $account = Account::factory()->create();

    Livewire::test(Form::class)
        ->set('form.label', 'Test Deposit')
        ->set('form.date', now()->format('Y-m-d'))
        ->set('form.amount_net', '50.00')
        ->set('form.vat', 19)
        ->set('form.amount_gross', '59.50')
        ->set('form.account_id', $account->id)
        ->set('form.type', 'Deposit')
        ->set('form.status', 'submitted')
        ->call('submitTransaction')
        ->assertHasNoErrors();

    $transaction = Transaction::where('label', 'Test Deposit')->first();
    expect($transaction)->not->toBeNull()
        ->and($transaction->type->value)->toBe('Deposit');
});

test('transaction creation validates required fields', function (): void {
    Livewire::test(Form::class)
        ->call('submitTransaction')
        ->assertHasErrors([
            'form.amount_net' => __('transaction.form.validation.amount_net_required'),
            'form.amount_gross' => __('transaction.form.validation.amount_gross_required'),
            'form.account_id' => __('transaction.form.validation.account_id_required'),
        ]);
});

test('amounts are stored as cent integers', function (): void {
    $account = Account::factory()->create();

    Livewire::test(Form::class)
        ->set('form.label', 'Amount Test')
        ->set('form.date', now()->format('Y-m-d'))
        ->set('form.amount_net', '99.99')
        ->set('form.vat', 19)
        ->set('form.amount_gross', '118.99')
        ->set('form.account_id', $account->id)
        ->set('form.type', 'Withdrawal')
        ->set('form.status', 'submitted')
        ->call('submitTransaction')
        ->assertHasNoErrors();

    $transaction = Transaction::where('label', 'Amount Test')->first();
    expect($transaction->amount_gross)->toBe(11899)
        ->and($transaction->amount_net)->toBe(9999);
});

test('the transaction wizard allows continuing without a document', function (): void {
    $account = Account::factory()->create();

    Livewire::test(Form::class, ['wizard' => true])
        ->call('nextStep')
        ->assertSet('step', 2)
        ->set('form.label', 'Wizard withdrawal')
        ->set('form.date', now()->format('Y-m-d'))
        ->set('form.amount_net', '100.00')
        ->set('form.vat', 19)
        ->set('form.amount_gross', '119.00')
        ->set('form.type', 'Withdrawal')
        ->call('nextStep')
        ->assertSet('step', 3)
        ->set('form.account_id', $account->id)
        ->call('nextStep')
        ->assertSet('step', 4)
        ->assertSee(__('transaction.create.steps.no_document_attached'))
        ->call('submitTransaction')
        ->assertHasNoErrors();

    expect(Transaction::where('label', 'Wizard withdrawal')->exists())->toBeTrue();
});

test('the transaction wizard keeps an uploaded document through review and attaches it on save', function (): void {
    Storage::fake('local');

    $account = Account::factory()->create();
    $file = UploadedFile::fake()->image('receipt.jpg');

    Livewire::test(Form::class, ['wizard' => true])
        ->set('documentFiles', [$file])
        ->set('documentCategory', 'receipt')
        ->call('nextStep')
        ->assertSet('step', 2)
        ->set('form.label', 'Wizard with receipt')
        ->set('form.date', now()->format('Y-m-d'))
        ->set('form.amount_net', '100.00')
        ->set('form.vat', 19)
        ->set('form.amount_gross', '119.00')
        ->set('form.type', 'Withdrawal')
        ->call('nextStep')
        ->set('form.account_id', $account->id)
        ->call('nextStep')
        ->assertSet('step', 4)
        ->assertSee('receipt.jpg')
        ->call('submitTransaction')
        ->assertHasNoErrors();

    $transaction = Transaction::where('label', 'Wizard with receipt')->firstOrFail();
    $document = Document::where('original_name', 'receipt.jpg')->firstOrFail();

    expect($document->documentable->is($transaction))->toBeTrue()
        ->and(Storage::disk('local')->exists($document->path))->toBeTrue();
});

test('the standalone transaction page starts with the document step', function (): void {
    Livewire::test(CreatePage::class)
        ->assertSee(__('transaction.create.steps.documents'))
        ->assertSee(__('transaction.create.steps.continue_without_document'))
        ->assertSee('wire:loading.attr="disabled"', false)
        ->assertSee('wire:target="documentFiles"', false);
});

test('the transaction wizard validates each step before advancing', function (): void {
    Livewire::test(Form::class, ['wizard' => true])
        ->call('nextStep')
        ->assertSet('step', 2)
        ->call('nextStep')
        ->assertHasErrors([
            'form.label' => __('transaction.form.validation.label_required'),
            'form.amount_net' => __('transaction.form.validation.amount_net_required'),
            'form.amount_gross' => __('transaction.form.validation.amount_gross_required'),
        ])
        ->assertSet('step', 2);
});

test('the transaction wizard requires review before saving', function (): void {
    Livewire::test(Form::class, ['wizard' => true])
        ->call('submitTransaction')
        ->assertHasErrors(['step']);

    expect(Transaction::count())->toBe(0);
});

test('a booked transaction requires a booking account in the assignment step', function (): void {
    $account = Account::factory()->create();

    Livewire::test(Form::class, ['wizard' => true])
        ->call('nextStep')
        ->set('form.label', 'Booked without account')
        ->set('form.date', now()->format('Y-m-d'))
        ->set('form.amount_net', '100.00')
        ->set('form.vat', 19)
        ->set('form.amount_gross', '119.00')
        ->set('form.type', 'Withdrawal')
        ->call('nextStep')
        ->set('form.account_id', $account->id)
        ->set('form.status', 'booked')
        ->call('nextStep')
        ->assertHasErrors(['form.booking_account_id' => 'required'])
        ->assertSet('step', 3);
});

test('the selected tax sphere filters booking accounts in the wizard', function (): void {
    BookingAccount::factory()->create([
        'number' => '1000',
        'label' => 'Ideeller Zweck',
        'area' => BookingAccountArea::IDEAL,
    ]);
    BookingAccount::factory()->create([
        'number' => '2000',
        'label' => 'Vermögensverwaltung',
        'area' => BookingAccountArea::ASSET_MANAGEMENT,
    ]);

    Livewire::test(Form::class, ['wizard' => true])
        ->call('nextStep')
        ->set('form.label', 'Sphere filter test')
        ->set('form.date', now()->format('Y-m-d'))
        ->set('form.amount_net', '100.00')
        ->set('form.vat', 19)
        ->set('form.amount_gross', '119.00')
        ->set('form.type', 'Withdrawal')
        ->call('nextStep')
        ->set('form.area', BookingAccountArea::IDEAL->value)
        ->assertSee('1000 - Ideeller Zweck')
        ->assertDontSee('2000 - Vermögensverwaltung');
});

test('choosing a booking account fills its sphere when none is selected', function (): void {
    $bookingAccount = BookingAccount::factory()->create([
        'area' => BookingAccountArea::IDEAL,
    ]);

    Livewire::test(Form::class, ['wizard' => true])
        ->set('form.booking_account_id', $bookingAccount->id)
        ->assertSet('form.area', BookingAccountArea::IDEAL)
        ->set('form.area', BookingAccountArea::ASSET_MANAGEMENT->value)
        ->assertSet('form.booking_account_id', null);
});
