<ul
    class="menu w-full gap-1 overflow-visible bg-base-200 px-2 transition-all duration-200 lg:menu-normal"
    :class="sidebarCompact ? 'menu-compact' : ''"
>
    @foreach($menuItems as $item)
        <x-lazy-menu-item :item="$item" />
    @endforeach
</ul>
