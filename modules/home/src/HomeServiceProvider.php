<?php

namespace SynApps\Modules\Home;

use Illuminate\Support\ServiceProvider;
use VmEngine\Synapse\Traits\AutoRegistersComponents;

class HomeServiceProvider extends ServiceProvider
{
    use AutoRegistersComponents;
    public function register() {
        $this->registerComponents();
    }

    public function boot() {}
}
