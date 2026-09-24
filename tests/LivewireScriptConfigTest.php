<?php

it('includes Livewire script configuration before the admin bundle', function () {
    $layout = file_get_contents(__DIR__.'/../resources/views/base-layout.blade.php');

    expect($layout)
        ->toContain('@livewireScriptConfig')
        ->and(strpos($layout, '@livewireScriptConfig'))
        ->toBeLessThan(strpos($layout, '@vite(config(\'lazy.admin.scripts\'))'));
});
