@props(['class' => ''])

@php
    $logo = app(\App\Services\SettingsService::class)->getLogo();
@endphp

@if($logo)
    <img src="{{ $logo }}" {{ $attributes->merge(['class' => $class]) }} alt="{{ setting('organization.name', config('branding.organization.name')) }}">
@else
    <x-application-logo {{ $attributes->merge(['class' => $class]) }} />
@endif