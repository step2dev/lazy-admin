<div {{ $attributes }}>
    @if($title)
        <h2 class="text-lg font-semibold">{{ $title }}</h2>
    @endif

    @if($description)
        <p class="mt-2 text-sm opacity-70">{{ $description }}</p>
    @endif

    @if(trim((string) $slot) !== '')
        <div class="mt-4">
            {{ $slot }}
        </div>
    @endif
</div>
