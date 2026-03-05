<?php

declare(strict_types=1);

use Livewire\Attributes\Layout;
use Livewire\Component;
use VmEngine\Synapse\Models\Config;
use VmEngine\Synapse\Services\Helper\Breadcrumbs;

new #[Layout('synapps::components.layouts.layout')] class extends Component
{
    public string $search = '';

    public int $perPage = 20;

    protected bool $hasMoreRecords = false;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount(): void
    {
        synav()->setActiveMenu('settings.configuration');
    }

    public function loadMore(): void
    {
        $this->perPage += 20;
    }

    public function updateConfig(int $id, string $value): void
    {
        $config = Config::findOrFail($id);
        $config->value = $value;
        $config->save();

        $this->dispatch('notify',
            variant: 'success',
            title: __('settings::page.configuration.saved'),
            message: ''
        );
    }

    public function updatedSearch(): void
    {
        $this->perPage = 20;
    }

    public function getConfigsProperty()
    {
        $configs = $this->buildQuery()->limit($this->perPage + 1)->get();
        $this->hasMoreRecords = $configs->count() > $this->perPage;

        return $configs->take($this->perPage);
    }

    public function getHasMoreProperty(): bool
    {
        return $this->hasMoreRecords ?? false;
    }

    public function getBreadcrumbsProperty()
    {
        return Breadcrumbs::make(
            label: 'Settings',
            icon: 'fa-solid fa-gear',
        )->add(
            label: 'Configuration',
            icon: 'fa-solid fa-sliders',
        );
    }

    protected function buildQuery()
    {
        $query = Config::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('label', 'like', '%'.$this->search.'%')
                    ->orWhere('key', 'like', '%'.$this->search.'%')
                    ->orWhere('group', 'like', '%'.$this->search.'%')
                    ->orWhere('value', 'like', '%'.$this->search.'%');
            });
        }

        $query->orderBy('group', 'asc')
            ->orderBy('key', 'asc');

        return $query;
    }
};
