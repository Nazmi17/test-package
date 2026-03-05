<?php

it('check if resource files is patched', function () {
    expect(file_get_contents(resource_path('css/app.css')))
        ->toContain('resources/css/synapps.css');
    expect(file_get_contents(resource_path('js/app.js')))
        ->toContain('resources/js/synapps.js');
});
