@props(['name' => ''])

@php
    // Initialen aus erstem und letztem Wort (Vor- + Nachname), lokal gerendert.
    // Ersetzt den frueheren ui-avatars.com-Request (DSGVO: keine Mitgliedernamen an Dritte).
    $words = preg_split('/\s+/u', trim($name)) ?: [];
    $first = $words[0] ?? '';
    $last = count($words) > 1 ? end($words) : '';
    $initials = mb_strtoupper(mb_substr($first, 0, 1).mb_substr($last, 0, 1));
    if ($initials === '') {
        $initials = '?';
    }
@endphp

<div aria-hidden="true"
    {{ $attributes->class(['flex select-none items-center justify-center bg-primary/10 font-semibold text-primary']) }}
>{{ $initials }}</div>
