<?php

declare(strict_types=1);

use Livewire\Attributes\Layout;
use Livewire\Component;
use VmEngine\Synapse\Models\Language;
use VmEngine\Synapse\Services\Helper\Breadcrumbs;

new #[Layout('synapps::components.layouts.layout')] class extends Component
{
    public string $search = '';

    public string $currentLanguage = 'en';

    public bool $multiLanguageEnabled = false;

    public array $enabledLanguages = [];

    public int $perPage = 20;

    protected bool $hasMoreRecords = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'currentLanguage' => ['except' => 'en'],
    ];

    public function mount(): void
    {
        $this->multiLanguageEnabled = config('synapps.apps.multi_language', false);
        $this->enabledLanguages = config('synapps.apps.enabled_languages', ['en']);

        if (! in_array($this->currentLanguage, $this->enabledLanguages)) {
            $this->currentLanguage = $this->enabledLanguages[0] ?? 'en';
        }

        synav()->setActiveMenu('settings.languages');
    }

    public function setLanguage(string $languageCode): void
    {
        $this->currentLanguage = $languageCode;
        $this->perPage = 20;
    }

    public function loadMore(): void
    {
        $this->perPage += 20;
    }

    public function updateLanguage(int $id, string $value): void
    {
        $language = Language::findOrFail($id);
        $language->value = $value;
        $language->save();

        $this->dispatch('notify',
            variant: 'success',
            title: __('settings::page.languages.saved'),
            message: ''
        );
    }

    public function updatedSearch(): void
    {
        $this->perPage = 20;
    }

    public function getLanguagesProperty()
    {
        $languages = $this->buildQuery()->limit($this->perPage + 1)->get();
        $this->hasMoreRecords = $languages->count() > $this->perPage;

        return $languages->take($this->perPage);
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
            label: 'Languages',
            icon: 'fa-solid fa-language',
        );
    }

    protected function buildQuery()
    {
        $query = Language::query();

        $query->where('language_code', $this->currentLanguage);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('label', 'like', '%'.$this->search.'%')
                    ->orWhere('key', 'like', '%'.$this->search.'%')
                    ->orWhere('value', 'like', '%'.$this->search.'%');
            });
        }

        $query->orderBy('key', 'asc');

        return $query;
    }
};
