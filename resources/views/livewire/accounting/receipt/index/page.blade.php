<div>

    <flux:heading size="lg">{{ __('receipt.title') }}</flux:heading>

    <nav>
        <!-- Navigation content -->
    </nav>

    <section class="grid gap-3 grid-cols-1 lg:grid-cols-3 2xl:grid-cols-6">
        @foreach($this->receipts as $receipt)
            <x-receipt-card :receipt="$receipt" />
        @endforeach
    </section>

{{ $this->receipts->links() }}
</div>