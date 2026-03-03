<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds GDPR fields to members table.
 *
 * Legal basis values (Art. 6 DSGVO):
 *  'contract'            → Art. 6 I b  – Membership contract (default)
 *  'legitimate_interest' → Art. 6 I f  – Legitimate organisational interest
 *  'consent'             → Art. 6 I a  – Explicit consent (e.g. honorary members)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table): void {

            // Timestamp when member consented to data processing (on application).
            $table->timestamp('gdpr_consent_at')->nullable()->after('applied_at');

            // Art. 6 DSGVO legal basis for this member's data.
            $table->string('gdpr_legal_basis')->default('contract')->after('gdpr_consent_at');

            // Optional: separate newsletter opt-in (Art. 6 I a).
            $table->timestamp('newsletter_consent_at')->nullable()->after('gdpr_legal_basis');
            $table->timestamp('newsletter_consent_revoked_at')->nullable()->after('newsletter_consent_at');

            // Optional: separate opt-in for photos/public media.
            $table->timestamp('photo_consent_at')->nullable()->after('newsletter_consent_revoked_at');
            $table->timestamp('photo_consent_revoked_at')->nullable()->after('photo_consent_at');

            // Set by PseudonymizeMembersCommand after retention period expires.
            $table->timestamp('pseudonymized_at')->nullable()->after('left_at');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table): void {
            $table->dropColumn([
                'gdpr_consent_at',
                'gdpr_legal_basis',
                'newsletter_consent_at',
                'newsletter_consent_revoked_at',
                'photo_consent_at',
                'photo_consent_revoked_at',
                'pseudonymized_at',
            ]);
        });
    }
};
