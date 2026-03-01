<?php

// tests/Arch/EnumUsageTest.php

arch('enums should use instance methods not static methods')
    ->expect('App\Enums')
    ->toOnlyBeUsedIn([
        'App\Models',
        'App\Livewire',
        'App\Services',
        'App\Actions',
        'App\Pdfs',
        'App\Mail',
    ]);

arch('transaction type static calls should not exist')
    ->expect('App\Enums\TransactionType')
    ->not->toBeUsed()
    ->ignoring([
        'App\Enums\TransactionType::cases',
        'App\Enums\TransactionType::from',
        'App\Enums\TransactionType::tryFrom',
        'App\Enums\TransactionType::toArray',
        'App\Enums\TransactionType::options',
        'App\Enums\TransactionType::incomeTypes',
        'App\Enums\TransactionType::expenseTypes',
    ]);

arch('transaction status static calls should not exist')
    ->expect('App\Enums\TransactionStatus')
    ->not->toBeUsed()
    ->ignoring([
        'App\Enums\TransactionStatus::cases',
        'App\Enums\TransactionStatus::from',
        'App\Enums\TransactionStatus::tryFrom',
        'App\Enums\TransactionStatus::toArray',
        'App\Enums\TransactionStatus::options',
    ]);
