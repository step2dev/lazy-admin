<ul>
    @foreach($menuItems as $menu)
        <li>
            <a href="{{ $menu['route'] }}">{{ $menu['label']}}</a>
            @if(count($menu['children'] ?? []))
                <ul>
                    @include('menu-generator', ['menuItems' => $menu->children])
                </ul>
            @endif
        </li>
    @endforeach
</ul>
