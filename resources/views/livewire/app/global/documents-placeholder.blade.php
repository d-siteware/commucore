<div class="space-y-6 animate-pulse" aria-hidden="true">

    {{-- Upload-Button Skeleton --}}
    <div class="h-8 w-40 rounded-md bg-zinc-200 dark:bg-zinc-700"></div>

    {{-- Tabellen-Header Skeleton --}}
    <div class="space-y-2">
        <div class="flex gap-4 border-b border-zinc-200 dark:border-zinc-700 pb-2">
            <div class="h-4 w-48 rounded bg-zinc-200 dark:bg-zinc-700"></div>
            <div class="h-4 w-24 rounded bg-zinc-200 dark:bg-zinc-700 hidden md:block"></div>
            <div class="h-4 w-16 rounded bg-zinc-200 dark:bg-zinc-700 hidden lg:block"></div>
            <div class="h-4 w-28 rounded bg-zinc-200 dark:bg-zinc-700 hidden lg:block"></div>
            <div class="ml-auto h-4 w-16 rounded bg-zinc-200 dark:bg-zinc-700"></div>
        </div>

        {{-- 3 Zeilen --}}
        @foreach(range(1, 3) as $_)
            <div class="flex gap-4 items-center py-3 border-b border-zinc-100 dark:border-zinc-800">
                {{-- Icon + Name --}}
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <div class="size-4 rounded bg-zinc-200 dark:bg-zinc-700 shrink-0"></div>
                    <div class="h-4 rounded bg-zinc-200 dark:bg-zinc-700 w-3/5"></div>
                </div>
                {{-- Kategorie --}}
                <div class="h-5 w-20 rounded-full bg-zinc-200 dark:bg-zinc-700 hidden md:block"></div>
                {{-- Größe --}}
                <div class="h-4 w-12 rounded bg-zinc-200 dark:bg-zinc-700 hidden lg:block"></div>
                {{-- Uploader --}}
                <div class="h-4 w-24 rounded bg-zinc-200 dark:bg-zinc-700 hidden lg:block"></div>
                {{-- Button --}}
                <div class="h-7 w-28 rounded-md bg-zinc-200 dark:bg-zinc-700 ml-auto"></div>
            </div>
        @endforeach
    </div>

</div>