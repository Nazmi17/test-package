<div x-data="{ pageName: 'Languages', isHome: false }">
    @include('synapps::components.layouts.partials.breadcrumbs')

    <div class="space-y-4">
        {{-- Language Tabs (if multi-language enabled) --}}
        @if ($multiLanguageEnabled && count($enabledLanguages) > 1)
            <div class="card">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8 px-5">
                        @foreach ($enabledLanguages as $langCode)
                            <button
                                class="@if ($currentLanguage === $langCode) border-brand-500 text-brand-600 dark:text-brand-400 @else border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300 @endif border-b-2 px-1 py-4 text-sm font-medium transition-colors"
                                type="button"
                                wire:click="setLanguage('{{ $langCode }}')"
                            >
                                {{ strtoupper($langCode) }}
                            </button>
                        @endforeach
                    </nav>
                </div>
            </div>
        @endif

        {{-- Search --}}
        <div class="card">
            <div class="card-body">
                <div class="max-w-md">
                    <label
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                        for="search"
                    >{{ __('settings::page.languages.search_placeholder') }}</label>
                    <input
                        class="form-input"
                        id="search"
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('settings::page.languages.search_placeholder') }}"
                    />
                </div>
            </div>
        </div>

        {{-- Languages List --}}
        <div class="space-y-3">
            @forelse ($this->languages as $language)
                <div
                    class="card"
                    wire:key="language-{{ $language->id }}"
                >
                    <div class="p-5">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            {{-- Key --}}
                            <div>
                                <label
                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                                    for="language-key-{{ $language->id }}"
                                >
                                    {{ __('settings::page.languages.label') }}
                                </label>
                                @if ($language->label)
                                    <div class="text-sm text-gray-800 dark:text-white/90">
                                        {{ $language->label }}
                                    </div>
                                @endif
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $language->key }}
                                </div>
                            </div>

                            {{-- Value --}}
                            <div>
                                <label
                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                                    for="language-value-{{ $language->id }}"
                                >
                                    {{ __('settings::page.languages.value') }}
                                </label>
                                <textarea
                                    class="form-input min-h-20"
                                    id="language-value-{{ $language->id }}"
                                    rows="3"
                                    wire:change="updateLanguage({{ $language->id }}, $event.target.value)"
                                >{{ $language->value }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="flex flex-col items-center justify-center p-10">
                        <i class="fa-solid fa-inbox mb-4 text-6xl text-gray-300 dark:text-gray-600"></i>
                        <p class="text-center text-gray-500 dark:text-gray-400">
                            {{ __('settings::page.languages.no_data') }}
                        </p>
                    </div>
                </div>
            @endforelse

            {{-- Infinite Scroll Trigger --}}
            @if ($this->hasMore)
                <div
                    class="flex justify-center py-8"
                    x-data="{
                        observer: null,
                        init() {
                            this.observer = new IntersectionObserver((entries) => {
                                if (entries[0].isIntersecting) {
                                    @this.call('loadMore')
                                }
                            }, {
                                rootMargin: '100px'
                            })
                            this.observer.observe(this.$el)
                        },
                        destroy() {
                            if (this.observer) {
                                this.observer.disconnect()
                            }
                        }
                    }"
                >
                    <div
                        class="flex items-center gap-2 text-gray-500 dark:text-gray-400"
                        wire:loading
                        wire:target="loadMore"
                    >
                        <i class="fa-solid fa-spinner fa-spin"></i>
                        <span>{{ __('settings::page.languages.loading') }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
