<div>
    @if($widgets === [])
        <x-lazy-empty-state
            :title="__('No dashboard widgets yet')"
            :description="__('Installed modules can register widgets with the Lazy Admin dashboard registry.')"
        />
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach($widgets as $widget)
                <x-lazy-card :href="$widget['url']" :hover="(bool) $widget['url']">
                    <div class="card-body gap-2">
                        <span class="text-sm font-medium opacity-70">{{ $widget['label'] }}</span>
                        <strong class="text-3xl font-semibold">{{ $widget['value'] }}</strong>
                        @if($widget['description'])
                            <span class="text-xs opacity-60">{{ $widget['description'] }}</span>
                        @endif
                    </div>
                </x-lazy-card>
            @endforeach
        </div>
    @endif
</div>
