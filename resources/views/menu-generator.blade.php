<ul class="menu bg-base-200 w-56 rounded-box">
    @foreach($menuItems as $menu)
        <li>
            <a href="{{ $menu['url'] ?? $menu['route'] ?? '#' }}">{{ $menu['label']}}</a>
            @if(count($menu['children'] ?? []))
                <ul>
                    @foreach($menu['children'] as $child)
                        <li>
                            <a href="{{ $child['url'] ?? $child['route'] ?? '#' }}">{{ $child['label']}}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
</ul>
