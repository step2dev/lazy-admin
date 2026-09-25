<div {{ $attributes }}>
    <div tabindex="0">
        {{ $trigger }}
    </div>

    <div
        tabindex="0"
        @class([
            'dropdown-content z-50 mt-3 rounded-box border border-base-300 bg-base-100 p-2 shadow-xl',
            $width,
        ])
    >
        {{ $slot }}
    </div>
</div>
