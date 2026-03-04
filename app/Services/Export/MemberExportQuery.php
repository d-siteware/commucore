<?php

declare(strict_types=1);

namespace App\Services\Export;

use App\Enums\MemberType;
use App\Models\Membership\Member;
use Illuminate\Database\Eloquent\Builder;

/**
 * Builds a filtered Member query for exports.
 * Pseudonymised members are excluded by default unless explicitly included.
 *
 * @phpstan-type ExportFilters array{
 *     include_pseudonymized: bool,
 *     only_active: bool,
 *     member_types: string[],
 * }
 */
final class MemberExportQuery
{
    /**
     * @param array{
     *     include_pseudonymized?: bool,
     *     only_active?: bool,
     *     member_types?: string[],
     * } $filters
     */
    public static function build(array $filters): Builder
    {
        /** @var Builder<Member> $query */
        $query = Member::query()
            ->with(['activeRoles']);

        // Pseudonymisierte einschließen oder ausschließen
        if (! ($filters['include_pseudonymized'] ?? false)) {
            $query->whereNull('pseudonymized_at');
        }

        // Nur aktive Mitglieder (kein left_at)
        if ($filters['only_active'] ?? false) {
            $query->whereNull('left_at');
        }

        // Nach MemberType filtern
        $types = $filters['member_types'] ?? [];
        if ($types !== []) {
            $query->whereIn('type', $types);
        }

        return $query->orderBy('name')->orderBy('first_name');
    }
}
