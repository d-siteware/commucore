<?php

declare(strict_types=1);

return [
    'title' => 'Privacy Policy',
    'p_1' => 'Responsible within the meaning of data protection laws:',
    'p_2' => 'Represented by the board',

    'sections' => [
        [
            'header' => '1. General',
            'body' => 'The protection of your personal data is of particular concern to us. We process your data exclusively on the basis of the statutory provisions of the General Data Protection Regulation (GDPR) and the Federal Data Protection Act (BDSG). This privacy policy informs you about the most important aspects of data processing in the context of our club activities and our website.',
        ],
        [
            'header' => '2. Data processing on this website',
            'body' => 'This website processes personal data only to the extent necessary to provide a secure and functional offering. No analysis or tracking tools are used. The website uses only technically necessary session cookies to maintain the login session (Laravel Session Management). Data is not shared with third parties.',
        ],
        [
            'header' => '3. Member management',
            'body' => 'Data submitted by club members (name, address, email address, telephone number, date of birth, bank details and membership information) is processed on the basis of Art. 6 (1) lit. b GDPR for the performance of the membership contract. After termination of membership, personal master data is pseudonymized after a retention period of 3 years. Financial data (fee payments, transactions) is retained for 10 years in accordance with Section 147 AO and Section 257 HGB. All data changes are logged in an audit-proof audit log.',
        ],
        [
            'header' => '4. Newsletter and event information',
            'body' => 'Non-members can voluntarily register to receive club news and event information. The processing of the email address is based on Art. 6 (1) lit. a GDPR (consent). Consent is documented with a timestamp. You can revoke your consent at any time by clicking the unsubscribe link in any email. After unsubscribing, your data will be completely deleted after a transitional period of 30 days. Data is not shared with third parties.',
        ],
        [
            'header' => '5. Event registrations',
            'body' => 'When registering for events, name, email address and optional information (telephone, remarks) are processed on the basis of Art. 6 (1) lit. b GDPR. This data is used exclusively for the implementation of the respective event and is automatically deleted 30 days after the event date.',
        ],
        [
            'header' => '6. Hosting and technical operation',
            'body' => 'The website is operated on its own server (self-hosted). Email communication is handled via servers of Strato AG (Germany). Data protection requirements are complied with.',
        ],
        [
            'header' => '7. Cookies',
            'body' => 'This website does not use cookies for analysis or tracking purposes. Only a technically necessary session cookie is used, which is deleted when the browser is closed. Consent is not required for this in accordance with Section 25 (2) TTDSG.',
        ],
        [
            'header' => '8. Data security',
            'body' => 'All access to personal data in the backend is logged in an audit log. Access to member data is restricted to authorized users (board, treasurer). Documents are stored encrypted on private storage and can only be accessed via authenticated access.',
        ],
        [
            'header' => '9. Your rights',
            'body' => 'In accordance with the GDPR, you have the right at any time to access (Art. 15), rectification (Art. 16), erasure (Art. 17), restriction of processing (Art. 18) and data portability (Art. 20). Consent once given can be revoked at any time with effect for the future. You also have the right to lodge a complaint with the competent data protection supervisory authority.',
        ],
        [
            'header' => '10. Contact',
            'body' => 'If you have any questions about data protection, please contact:',
            'email' => true,
        ],
    ],
];
