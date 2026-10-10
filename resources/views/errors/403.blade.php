@php
    $status = 403;
    $title = __('lazy-admin::errors.403.title');
    $headline = __('lazy-admin::errors.403.headline');
    $message = __('lazy-admin::errors.403.message');
    $hint = __('lazy-admin::errors.403.hint');
    $eyebrow = __('lazy-admin::errors.403.eyebrow');
    $icon = 'shield';
@endphp
@if($useAdminLayout)
    @include('lazy::errors.partials.admin-layout')
@else
    @include('lazy::errors.partials.standalone')
@endif
