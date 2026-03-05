<?php

use Illuminate\Support\Facades\App;
use SynApps\Modules\Home\HomeServiceProvider;

describe('Home Module Discovery', function () {
    it('check module registered in composer.json', function () {
        expect(stripslashes(file_get_contents(base_path('composer.json'))))
            ->toContain(
                'SynApps\\Modules\\Home',
                'SynDB\\Modules\\Home'
            );
        expect(App::providerIsLoaded(HomeServiceProvider::class))->toBeTrue();
    });
});
