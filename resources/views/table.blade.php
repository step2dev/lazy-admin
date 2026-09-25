<div {{ $attributes }}>
    <table @class([
        'table',
        'table-zebra' => $zebra,
        'table-xs' => $compact,
    ])>
        {{ $slot }}
    </table>
</div>
