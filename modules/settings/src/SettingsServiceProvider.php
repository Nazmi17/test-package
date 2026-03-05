<?php

declare(strict_types=1);

namespace SynApps\Modules\Settings;

use Illuminate\Support\ServiceProvider;
use VmEngine\Synapse\Traits\AutoRegistersComponents;

class SettingsServiceProvider extends ServiceProvider
{
    use AutoRegistersComponents;

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerComponents();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
