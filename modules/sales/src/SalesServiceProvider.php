<?php

declare(strict_types=1);

namespace SynApps\Modules\Sales;

use Illuminate\Support\ServiceProvider;
use VmEngine\Synapse\Traits\AutoRegistersComponents;

class SalesServiceProvider extends ServiceProvider
{
    use AutoRegistersComponents;
    public function register(): void
    {
        $this->registerComponents();
    }

    public function boot(): void
    {
        //
    }
}
