<?php

declare(strict_types=1);

namespace SynApps\Modules\Inventory;

use Illuminate\Support\ServiceProvider;
use VmEngine\Synapse\Traits\AutoRegistersComponents;

class InventoryServiceProvider extends ServiceProvider
{
   use AutoRegistersComponents;

    public function register(): void
    {
        // Auto-register all Livewire and Blade components
        $this->registerComponents();
    }

    public function boot(): void
    {
        //
    }
}
