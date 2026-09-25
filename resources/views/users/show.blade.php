<x-lazy-layout>
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">{{ $user->name }}</h1>
                <p class="mt-1 text-sm opacity-70">{{ $user->email }}</p>
            </div>

            <div class="flex items-center gap-2">
                @if (Route::has(trim(config('lazy.admin.route.name', 'admin.'), '.').'.user.edit'))
                    <a
                        class="btn btn-warning btn-sm btn-square"
                        href="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.user.edit', $user->getKey()) }}"
                        title="{{ __('Edit') }}"
                        aria-label="{{ __('Edit') }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </a>
                @endif

                <a class="btn btn-ghost" href="{{ route(trim(config('lazy.admin.route.name', 'admin.'), '.').'.user.index') }}">
                    {{ __('Back') }}
                </a>
            </div>
        </div>

        <div class="rounded-box border border-base-300 bg-base-100 p-6">
            <dl class="grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm opacity-60">{{ __('ID') }}</dt>
                    <dd class="font-medium">{{ $user->getKey() }}</dd>
                </div>

                <div>
                    <dt class="text-sm opacity-60">{{ __('Name') }}</dt>
                    <dd class="font-medium">{{ $user->name }}</dd>
                </div>

                <div>
                    <dt class="text-sm opacity-60">{{ __('Email') }}</dt>
                    <dd class="font-medium">{{ $user->email }}</dd>
                </div>

                @if (! empty($user->full_name))
                    <div>
                        <dt class="text-sm opacity-60">{{ __('Full name') }}</dt>
                        <dd class="font-medium">{{ $user->full_name }}</dd>
                    </div>
                @endif

                <div>
                    <dt class="text-sm opacity-60">{{ __('Created at') }}</dt>
                    <dd class="font-medium">{{ $user->created_at?->format('d/m/Y H:i:s') ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="text-sm opacity-60">{{ __('Updated at') }}</dt>
                    <dd class="font-medium">{{ $user->updated_at?->format('d/m/Y H:i:s') ?? '—' }}</dd>
                </div>
            </dl>
        </div>
    </div>
</x-lazy-layout>
