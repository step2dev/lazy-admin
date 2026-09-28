@if ($breadcrumbItems)
    <nav aria-label="{{ __('Breadcrumb') }}">
        <x-lazy-breadcrumbs :items="$breadcrumbItems" />
    </nav>
@endif
