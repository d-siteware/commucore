<?php

declare(strict_types=1);

return [
    'title' => 'SEPA Direct Debit Mandate',
    'debtor' => 'Debtor',
    'name' => 'Name',
    'street' => 'Street',
    'zip_city' => 'ZIP/City',
    'country' => 'Country',
    'creditor' => 'Creditor',
    'mandate_text' => 'I authorize the creditor to collect payments from my account by direct debit. At the same time, I instruct my credit institution to honour the direct debits drawn on my account by the creditor.',
    'mandate_type' => 'Type of Direct Debit',
    'mandate_type_core' => 'Core Direct Debit (CORE)',
    'mandate_type_b2b' => 'Business-to-Business Direct Debit (B2B)',
    'hint' => 'Note',
    'hint_core' => 'I can request a refund of the debited amount within eight weeks, starting from the debit date. The conditions agreed with my credit institution apply.',
    'hint_b2b' => 'As this is a SEPA business-to-business direct debit (B2B), I have no right to a refund of the debited amount after the direct debit has been honoured. I confirm that I am not acting as a consumer when issuing this mandate.',
    'fee_info' => 'Contribution Information (for information only, not part of the mandate authorization)',
    'fee_amount' => 'Contribution Amount',
    'fee_interval' => 'Interval',
    'fee_per_year' => 'Expected Collections per Year',
    'fee_hint' => 'Note: This information is purely informative. The SEPA direct debit mandate allows the creditor to collect all due payments regardless of amount and frequency.',
    'location_date' => 'Place, Date',
    'signature' => 'Signature',
];
