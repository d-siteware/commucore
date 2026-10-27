@script
<script>
    Alpine.data('checkVat', () => ({
        updateValuesFromGross() {
            const grossCents = this.updateCents(this.$wire.form.amount_gross);
            const vat = parseFloat(this.$wire.form.vat) || 0;
            const taxCents = Math.round((grossCents * vat) / (100 + vat));
            const netCents = grossCents - taxCents;

            this.$wire.form.tax = this.maskInput(taxCents / 100);
            this.$wire.form.amount_net = this.maskInput(netCents / 100);
            this.$wire.form.amount_gross = this.maskInput(grossCents / 100);
        },

        updateValuesFromNet() {
            const netCents = this.updateCents(this.$wire.form.amount_net);
            const vat = parseFloat(this.$wire.form.vat) || 0;
            const grossCents = Math.round(netCents * (100 + vat) / 100);
            const taxCents = grossCents - netCents;

            this.$wire.form.tax = this.maskInput(taxCents / 100);
            this.$wire.form.amount_gross = this.maskInput(grossCents / 100);
            this.$wire.form.amount_net = this.maskInput(netCents / 100);
        },

        updateCents(formattedValue) {
            const value = (formattedValue || '0')
                .replace(/[^\d,]/g, '')
                .replace(',', '.');

            return Math.round((parseFloat(value) || 0) * 100);
        },

        maskInput(value) {
            return new Intl.NumberFormat('de-DE', {
                style: 'decimal',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(value);
        },
    }));
</script>
@endscript
