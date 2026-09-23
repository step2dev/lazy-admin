<ul class="menu bg-base-200 w-56 rounded-box">
    @foreach($menuItems as $item)
        @include('lazy::menu-item', ['item' => $item])
    @endforeach
</ul>
