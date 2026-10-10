@php
    $status = 404;
    $title = __('lazy-admin::errors.404.title');
    $headline = __('lazy-admin::errors.404.headline');
    $message = __('lazy-admin::errors.404.message');
    $hint = __('lazy-admin::errors.404.hint');
    $eyebrow = __('lazy-admin::errors.404.eyebrow');
    $icon = 'search';
@endphp
@if($useAdminLayout)
    @include('lazy::errors.partials.admin-layout')
@else
    @include('lazy::errors.partials.standalone')
@endif
