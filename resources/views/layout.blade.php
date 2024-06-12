@props([
    'header' => '',
    'wrapper' => '',
    'title' => '',
    'action' => '',
    'breadcrumb' => '',
    'footer' => '',
    'meta' => '',
    'styles' => '',
    'scripts' => '',
    'noscript' => '',
])
@use('Step2dev\LazyAdmin\Facades\Breadcrumb')
<x-lazy-base-layout>
    @if($header)
        <header class="navbar bg-base-200">
            {{ $header }}
        </header>
    @else
        <x-lazy-header/>
    @endif
    <div class="content flex flex-col md:flex-row">
        <aside
            class="sidebar-menu border-primary bg-base-200 border-y-2 py-4 transform transition-all"
            aria-label="Sidebar">

        </aside>
        <div class="bg-base-100 min-h-full w-full overflow-x-auto transition-all">
            <!-- Page Heading -->
            @if (! $header)
                <div class="bg-base shadow dark:bg-indigo-900">
                    <div class="mx-auto grid grid-cols-3 gap-4 content-center py-6 px-4 sm:px-6 lg:px-8">
                        <div class="min-w-0 col-span-3 lg:col-span-2">
                            <h2 class="text-xl font-semibold leading-tight">
                                {{ $title ?: __(Route::currentRouteName()) }}
                            </h2>
                            @if($breadcrumb)
                                {{ $breadcrumb }}
                            @else
                                {{ Breadcrumb::render() }}
                            @endif
                        </div>
                        <div class="col-span-3 lg:col-span-1 flex justify-end flex-wrap lg:mt-0 lg:ml-4 ">
                            <x-lazy-join>
                                @if($action)
                                    {{ $action }}
                                @endif

                                @if($routes['create'])
                                    <x-lazy-btn
                                        :href="$routes['create']['url']"
                                        outline accent sm :label="__('lazy.btn.create')" sm/>
                                @endif
                                @if($routes['show'])
                                    <x-lazy-btn
                                        :href="$routes['show']['url']"
                                        outline info sm :label="__('lazy.btn.show')" sm
                                        target="{{ $routes['show']['target'] }}"/>
                                @endif
                                @if($routes['edit'])
                                    <x-lazy-btn
                                        :href="$routes['edit']['url']"
                                        outline info sm :label="__('lazy.btn.edit')" sm/>
                                @endif
                                @if($routes['destroy'])
                                    <x-lazy-btn-delete
                                        :href="$routes['destroy']['url']"
                                        :label="__('lazy.btn.delete')"
                                    />
                                @endif
                                @if (str(Route::currentRouteName())->contains(['index', 'show', 'edit', 'create']))
                                    <x-lazy-btn-back/>
                                @endif
                            </x-lazy-join>
                        </div>
                    </div>
                </div>
            @else
                {!! $header !!}
            @endif
            <!-- Page Heading -->
            <!-- Page Content -->
            <main class="mx-auto min-h-full py-6 px-4 shadow sm:px-6 lg:px-8">
                {{ $slot }}
            </main>
            <!-- Page Content -->
        </div>
    </div>
    @if($footer)
        <footer
            class="footer bg-base-200 items-center p-4">
            {{ $footer }}
        </footer>
    @else
        <x-lazy-footer/>
    @endif
</x-lazy-base-layout>
