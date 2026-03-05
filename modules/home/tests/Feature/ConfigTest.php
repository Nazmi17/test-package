<?php

describe('SynapseConfig', function () {
    it('check if synapse config file is published', function () {
        expect(file_exists(config_path('synapse.php')))->toBeTrue();
        expect(file_exists(config_path('synapps.php')))->toBeTrue();
    });
    it('check if main config file exists', function () {
        expect(file_exists(synapps_path('config/apps.json')))->toBeTrue();
        expect(file_exists(synapps_path('config/system.json')))->toBeTrue();
    });
    it('validate apps.json config', function () {
        $configArray = json_decode(
            file_get_contents(synapps_path('config/apps.json')),
            true
        );
        expect($configArray)
            // ->dd()
            ->toHaveKeys([
                'apps_code',
                'app_name',
                'author',
                'email',
                'urls.domain.dev',
                'urls.domain.prod',
                'urls.prefix',
                'urls.api_prefix',
                'title',
            ]);
    });
    it('validate system.json config', function () {
        $configArray = json_decode(
            file_get_contents(synapps_path('config/system.json')),
            true
        );
        expect($configArray)
            // ->dd()
            ->toHaveKeys([
                'env',
                'laravel_key',
            ]);
    });
    it('check if config files compiled properly', function () {
        expect(file_exists(synapps_path('config/compiled/apps.json')))->toBeTrue();
        expect(file_exists(synapps_path('config/compiled/system.json')))->toBeTrue();
        expect(file_exists(synapps_path('config/compiled/binding.json')))->toBeTrue();
        expect(file_exists(synapps_path('config/compiled/checksum')))->toBeTrue();

        $compiledApps = json_decode(
            file_get_contents(synapps_path('config/compiled/apps.json')),
            true
        );
        expect($compiledApps)
            // ->dd()
            ->toHaveKeys([
                'apps_code',
                'app_name',
                'author',
                'email',
                'urls.domain.dev',
                'urls.domain.prod',
                'urls.prefix',
                'urls.api_prefix',
                'title',
            ]);
        $compiledSystem = json_decode(
            file_get_contents(synapps_path('config/compiled/system.json')),
            true
        );
        expect($compiledSystem)
            // ->dd()
            ->toHaveKeys([
                'env',
                'laravel_key',
            ]);
        $compiledBinding = json_decode(
            file_get_contents(synapps_path('config/compiled/binding.json')),
            true
        );
        expect($compiledBinding)
            // ->dd()
            ->toHaveKeys([
                'controller',
                'interface',
                'route',
            ]);
    });
});
