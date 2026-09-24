<ul
    class="menu bg-base-200 w-full overflow-visible transition-all duration-200 lg:menu-normal"
    :class="sidebarCompact ? 'menu-compact' : ''"
>
    @foreach($menuItems as $item)
        @include('lazy::menu-item', ['item' => $item])
    @endforeach
</ul>
