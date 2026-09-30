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
    'menu' => '',
    'routes' => [],
])
@use('Step2dev\LazyMenu\Facades\Menu')
<x-lazy-base-layout
    :title="$title"
    :meta="$meta"
    :styles="$styles"
    :scripts="$scripts"
    :noscript="$noscript"
>
    <div
        x-data="{
            sidebarCompact: localStorage.getItem('lazy-admin-sidebar-compact') === '1',
            mobileSidebarOpen: false,
            toggleSidebar() {
                if (window.matchMedia('(max-width: 767px)').matches) {
                    this.mobileSidebarOpen = ! this.mobileSidebarOpen;

                    return;
                }

                this.sidebarCompact = ! this.sidebarCompact;
                localStorage.setItem('lazy-admin-sidebar-compact', this.sidebarCompact ? '1' : '0');
            },
            closeMobileSidebar() {
                this.mobileSidebarOpen = false;
            }
        }"
        @lazy-sidebar-toggle.window="toggleSidebar()"
        class="contents"
    >
    @if($header)
        <header class="navbar bg-base-200">
            {{ $header }}
        </header>
    @else
        <x-lazy-header/>
    @endif
    <div class="content flex min-h-0 flex-1 flex-col md:flex-row">
        <div
            x-cloak
            x-show="mobileSidebarOpen"
            x-transition.opacity
            class="fixed inset-0 z-40 bg-black/40 backdrop-blur-[1px] md:hidden"
            aria-hidden="true"
            @click="closeMobileSidebar()"
        ></div>
        <aside
            class="sidebar-menu fixed inset-y-0 left-0 z-50 w-[min(20rem,85vw)] overflow-y-auto border-r border-base-300 bg-base-200 py-3 shadow-2xl transition-transform duration-200 md:sticky md:top-16 md:z-auto md:h-[calc(100vh-4rem)] md:w-auto md:shrink-0 md:translate-x-0 md:overflow-y-auto md:border-b-0 md:border-r md:shadow-none"
            :class="[mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCompact ? 'md:w-20' : 'md:w-64']"
            aria-label="Sidebar"
            :aria-expanded="(mobileSidebarOpen || ! sidebarCompact).toString()"
            @keydown.escape.window="closeMobileSidebar()"
            @click="if ($event.target.closest('a')) closeMobileSidebar()">
            @if($menu)
                {{ $menu }}
            @else
                {!! Menu::render() !!}
            @endif
        </aside>
        <div class="min-h-full min-w-0 flex-1 bg-base-100 transition-all">
            <!-- Page Heading -->
            @if (! $header)
                <div class="border-b border-base-300 bg-base-100">
                    <div class="mx-auto grid grid-cols-3 content-center gap-4 px-4 py-5 sm:px-6 lg:px-8">
                        <div class="min-w-0 col-span-3 lg:col-span-2">
                            <h2 class="text-xl font-semibold leading-tight">
                                {{ $title ?: __(Route::currentRouteName()) }}
                            </h2>
                            @if($breadcrumb)
                                {{ $breadcrumb }}
                            @else
                                @include('lazy::breadcrumb-trail')
                            @endif
                        </div>
                        <div class="col-span-3 flex flex-wrap justify-start gap-2 lg:col-span-1 lg:justify-end">
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
            <main class="mx-auto min-h-full px-4 py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>
            <!-- Page Content -->
        </div>
    </div>
    @if($footer)
        <footer
            class="footer sm:footer-horizontal bg-base-200 items-center p-4">
            {{ $footer }}
        </footer>
    @else
        <x-lazy-footer/>
    @endif
    </div>
</x-lazy-base-layout>
