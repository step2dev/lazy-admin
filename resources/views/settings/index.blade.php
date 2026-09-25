<div class="grid grid-cols-1 gap-6 lg:grid-cols-[16rem_minmax(0,1fr)]">
    <nav class="flex flex-col gap-1 rounded-2xl border border-base-300 bg-base-100 p-2" aria-label="{{ __('Settings sections') }}">
        @foreach($sections as $item)
            @if($activeSection['id'] === $item['id'])
                <x-lazy-btn
                    type="button"
                    wire:click="selectSection('{{ $item['id'] }}')"
                    primary
                    class="h-auto w-full justify-start rounded-xl px-4 py-3 text-left"
                >
                    <span>
                        <span class="block font-medium">{{ $item['label'] }}</span>
                        @if($item['description'])
                            <span class="mt-1 block text-xs opacity-70">{{ $item['description'] }}</span>
                        @endif
                    </span>
                </x-lazy-btn>
            @else
                <x-lazy-btn
                    type="button"
                    wire:click="selectSection('{{ $item['id'] }}')"
                    ghost
                    class="h-auto w-full justify-start rounded-xl px-4 py-3 text-left"
                >
                    <span>
                        <span class="block font-medium">{{ $item['label'] }}</span>
                        @if($item['description'])
                            <span class="mt-1 block text-xs opacity-70">{{ $item['description'] }}</span>
                        @endif
                    </span>
                </x-lazy-btn>
            @endif
        @endforeach
    </nav>

    <div class="min-w-0">
        @livewire($activeSection['component'], [], key('settings-'.$activeSection['id']))
    </div>
</div>
