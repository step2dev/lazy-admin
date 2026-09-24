<ul
    class="menu bg-base-200 rounded-box w-full overflow-visible p-2 transition-all duration-200"
    :class="sidebarCompact ? 'menu-compact' : ''"
>
    @foreach($menuItems as $item)
        @include('lazy::menu-item', ['item' => $item])
    @endforeach
</ul>
