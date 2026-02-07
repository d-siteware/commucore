@php
    use Illuminate\Support\Facades\Vite;
    
    $settings = app(\App\Services\SettingsService::class);
    $faviconInfo = $settings->getFaviconInfo();
@endphp

@if($faviconInfo['type'] === 'svg')
    {{-- SVG Favicon (modern browsers) --}}
    <link rel="icon"
          href="{{ $faviconInfo['url'] }}"
          type="image/svg+xml"
    >
    {{-- Fallback für ältere Browser --}}
    <link rel="icon"
          href="{{ $faviconInfo['url'] }}"
          sizes="any"
    >

@elseif($faviconInfo['type'] === 'ico')
    {{-- ICO Favicon --}}
    <link rel="icon"
          href="{{ $faviconInfo['url'] }}"
          sizes="48x48"
    >

@elseif($faviconInfo['type'] === 'png')
    {{-- PNG Favicons in verschiedenen Größen --}}
    <link rel="icon"
          type="image/png"
          href="{{ $settings->getFaviconUrl('96') }}"
          sizes="96x96"
    />
    <link rel="apple-touch-icon"
          sizes="180x180"
          href="{{ $settings->getFaviconUrl('180') }}"
    />
    <link rel="apple-touch-icon"
          href="{{ $settings->getFaviconUrl('512') }}"
    />

@else
    {{-- Standard Vite Favicons --}}
    <link rel="icon"
          href="{{ Vite::asset('resources/images/favicon.svg') }}"
          sizes="48x48"
    >
    <link rel="icon"
          href="{{ Vite::asset('resources/images/logo_commu-core.svg') }}"
          sizes="any"
          type="image/svg+xml"
    >
    <link rel="apple-touch-icon"
          href="{{ Vite::asset('resources/images/web-app-manifest-512x512.png') }}"
    >
    <link rel="apple-touch-icon"
          sizes="180x180"
          href="{{ Vite::asset('resources/images/apple-touch-icon.png') }}"
    />
    <link rel="icon"
          type="image/png"
          href="{{ Vite::asset('resources/images/favicon-96x96.png') }}"
          sizes="96x96"
    />
@endif