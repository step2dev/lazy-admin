<ul
    class="menu bg-base-200 rounded-box transition-all duration-200"
    :class="sidebarCompact ? 'w-20 menu-compact' : 'w-56'"
>
    @foreach($menuItems as $item)
        @include('lazy::menu-item', ['item' => $item])
    @endforeach
</ul>
