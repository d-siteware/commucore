<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      class="scroll-smooth"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1"
    >

    <title>{{ setting('organization.name') }} | Maintenance </title>

    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<body class="font-sans antialiased bg-zinc-50 text-black/50 dark:bg-zinc-900 dark:text-white/50">

    <div class="relative min-h-screen flex flex-col items-center justify-between my-12">
        <main>
            <div class="grid place-items-center space-y-12">
                <div class="ml-6">
                    <x-application-logo class="size-16 md:size-28 lg:size-40"/>
                </div>

                <p class="lg:text-5xl font-semibold text-center">{{ setting('organization.name') }}</p>
                <h1 class="text-3xl mx-auto w-3/4 text-wrap hyphens-auto text-center">Die Seite erhält gerade ein Update und ist nicht verfügbar!</h1>
                <h2 class="text-xl mx-auto w-3/4 text-wrap hyphens-auto text-center">Bitte versuchen Sie es später noch einmal. Danke & bis gleich!</h2>

            </div>
        </main>
    </div>
</body>
</html>

