<?php

declare(strict_types=1);

arch('Member Create/Form uses HandlesErrors')
    ->expect('App\Livewire\Member\Create\Form')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Member Index/Page uses HandlesErrors')
    ->expect('App\Livewire\Member\Index\Page')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Member CancelMembership uses HandlesErrors')
    ->expect('App\Livewire\Member\CancelMembership')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Member Show/Page uses HandlesErrors')
    ->expect('App\Livewire\Member\Show\Page')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('CancellationRequest Create uses HandlesErrors')
    ->expect('App\Livewire\Member\CancellationRequest\Create')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('CancellationRequest Review uses HandlesErrors')
    ->expect('App\Livewire\Member\CancellationRequest\Review')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('ChangeRequest Create uses HandlesErrors')
    ->expect('App\Livewire\Member\ChangeRequest\Create')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('ChangeRequest Review uses HandlesErrors')
    ->expect('App\Livewire\Member\ChangeRequest\Review')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('SepaMandate Manage uses HandlesErrors')
    ->expect('App\Livewire\Member\SepaMandate\Manage')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Roles Page uses HandlesErrors')
    ->expect('App\Livewire\Member\Roles\Page')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Roles Form uses HandlesErrors')
    ->expect('App\Livewire\Member\Roles\Form')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Import PreviewStep uses HandlesErrors')
    ->expect('App\Livewire\Member\Import\Steps\PreviewStep')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Import ImportStep uses HandlesErrors')
    ->expect('App\Livewire\Member\Import\Steps\ImportStep')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Account Create/Form uses HandlesErrors')
    ->expect('App\Livewire\Accounting\Account\Create\Form')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Account Index/Page uses HandlesErrors')
    ->expect('App\Livewire\Accounting\Account\Index\Page')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Funding Create/Page uses HandlesErrors')
    ->expect('App\Livewire\Accounting\Funding\Create\Page')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Funding Show/Page uses HandlesErrors')
    ->expect('App\Livewire\Accounting\Funding\Show\Page')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Funding LinkProjectForm uses HandlesErrors')
    ->expect('App\Livewire\Accounting\Funding\LinkProjectForm')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Report Index/Page uses HandlesErrors')
    ->expect('App\Livewire\Accounting\Report\Index\Page')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Report Create/Form uses HandlesErrors')
    ->expect('App\Livewire\Accounting\Report\Create\Form')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Report Audit/Page uses HandlesErrors')
    ->expect('App\Livewire\Accounting\Report\Audit\Page')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Report CashCount Create/Form uses HandlesErrors')
    ->expect('App\Livewire\Accounting\Report\CashCount\Create\Form')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Transaction Create/Form uses HandlesErrors')
    ->expect('App\Livewire\Accounting\Transaction\Create\Form')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Transaction Index/Page uses HandlesErrors')
    ->expect('App\Livewire\Accounting\Transaction\Index\Page')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Transaction Cancel/Form uses HandlesErrors')
    ->expect('App\Livewire\Accounting\Transaction\Cancel\Form')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Transaction Booking/Form uses HandlesErrors')
    ->expect('App\Livewire\Accounting\Transaction\Booking\Form')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Transaction Boxoffice/Form uses HandlesErrors')
    ->expect('App\Livewire\Accounting\Transaction\Boxoffice\Form')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('SepaCollection Index/Page uses HandlesErrors')
    ->expect('App\Livewire\Accounting\SepaCollection\Index\Page')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('FiscalYear Create/Form uses HandlesErrors')
    ->expect('App\Livewire\Accounting\FiscalYear\Create\Form')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('FiscalYear Close/Page uses HandlesErrors')
    ->expect('App\Livewire\Accounting\FiscalYear\Close\Page')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('FiscalYear Index/Page uses HandlesErrors')
    ->expect('App\Livewire\Accounting\FiscalYear\Index\Page')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Accounting Index/Page uses HandlesErrors')
    ->expect('App\Livewire\Accounting\Index\Page')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Event Create/Page uses HandlesErrors')
    ->expect('App\Livewire\Activity\Event\Create\Page')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Event Show/Page uses HandlesErrors')
    ->expect('App\Livewire\Activity\Event\Show\Page')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Event Visitor Create/Form uses HandlesErrors')
    ->expect('App\Livewire\Activity\Event\Visitor\Create\Form')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Event Subscription Create/Form uses HandlesErrors')
    ->expect('App\Livewire\Activity\Event\Subscription\Create\Form')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Event Show EventPayment uses HandlesErrors')
    ->expect('App\Livewire\Activity\Event\Show\EventPayment')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Event PosterGenerator Create uses HandlesErrors')
    ->expect('App\Livewire\Activity\Event\PosterGenerator\Create')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Project Create/Page uses HandlesErrors')
    ->expect('App\Livewire\Activity\Project\Create\Page')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Project Show/Page uses HandlesErrors')
    ->expect('App\Livewire\Activity\Project\Show\Page')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Project LinkFundingForm uses HandlesErrors')
    ->expect('App\Livewire\Activity\Project\LinkFundingForm')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Blog Post Form uses HandlesErrors')
    ->expect('App\Livewire\Activity\Blog\Post\Form')
    ->toUse('App\Livewire\Traits\HandlesErrors');

arch('Blog Post EventSelector uses HandlesErrors')
    ->expect('App\Livewire\Activity\Blog\Post\EventSelector')
    ->toUse('App\Livewire\Traits\HandlesErrors');

it('Blog Post Create uses HandlesErrors trait', function (): void {
    $source = file_get_contents(dirname(__DIR__, 2).'/app/Livewire/Activity/Blog/Post/Create.php');
    expect($source)->toContain('use App\Livewire\Traits\HandlesErrors;');
});

arch('Blog Post Index/Page uses HandlesErrors')
    ->expect('App\Livewire\Activity\Blog\Post\Index\Page')
    ->toUse('App\Livewire\Traits\HandlesErrors');

it('ensures all mutation methods in Member Livewire components have try-catch', function (): void {
    $base = dirname(__DIR__, 2).'/app/Livewire/Member';
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($base, RecursiveDirectoryIterator::SKIP_DOTS),
    );
    $files = iterator_to_array($iterator);
    $baseLen = strlen($base) + 1;
    $missing = [];

    $mutationPatterns = [
        'store', 'save', 'create', 'update', 'delete', 'submit',
        'accept', 'reject', 'cancel', 'reactivate', 'import',
        'rollback', 'restore', 'link', 'unlink', 'detach', 'attach',
        'review', 'send', 'start',
    ];

    foreach ($files as $file) {
        $relative = substr($file->getPathname(), $baseLen);
        $class = 'App\\Livewire\\Member\\'
            . str_replace(['/', '.php'], ['\\', ''], $relative);

        if (! class_exists($class)) {
            continue;
        }

        $reflection = new ReflectionClass($class);
        $source = file_get_contents($file->getPathname());

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $name = $method->getName();

            if ($method->isStatic() || $method->isConstructor()
                || $name === 'mount' || $name === 'render' || $name === 'boot'
                || str_starts_with($name, 'updated') || str_starts_with($name, 'booted')
                || str_starts_with($name, 'rendered')
                || in_array($name, ['startReview', 'attachMemberRole', 'deleteProfileImage',
                    'editItem', 'bookItem', 'download', 'downloadDocument', 'openCreateForm',
                    'addRole', 'editRole', 'editMemberRole', 'removeMemberRole',
                    'checkBirthDate', 'checkEmail', 'addDummyData', 'resetForm',
                ], true)
            ) {
                continue;
            }

            $isMutation = (bool) array_filter(
                $mutationPatterns,
                fn (string $p): bool => str_starts_with($name, $p),
            );

            if (! $isMutation) {
                continue;
            }

            $startLine = $method->getStartLine();
            $endLine = $method->getEndLine();
            $lines = explode("\n", $source);
            $body = implode("\n", array_slice($lines, $startLine - 1, $endLine - $startLine + 1));

            if (! str_contains($body, 'try {') && ! str_contains($body, '->handleError(')) {
                $missing[] = "$class::$name()";
            }
        }
    }

    expect($missing)->toBeEmpty('Mutation methods without try-catch: '.implode(', ', $missing));
});