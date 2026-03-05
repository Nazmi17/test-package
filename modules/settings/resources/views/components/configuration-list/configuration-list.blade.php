<div x-data="{ pageName: 'Configurations', isHome: false }">
    @include('synapps::components.layouts.partials.breadcrumbs')

    <div class="space-y-4">
        {{-- Search --}}
        <div class="card">
            <div class="card-body">
                <div class="max-w-md">
                    <label
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                        for="search"
                    >{{ __('settings::page.configuration.search_placeholder') }}</label>
                    <input
                        class="form-input"
                        id="search"
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('settings::page.configuration.search_placeholder') }}"
                    />
                </div>
            </div>
        </div>

        {{-- Configuration List --}}
        <div class="space-y-3">
            @forelse ($this->configs as $config)
                <div
                    class="card"
                    wire:key="config-{{ $config->id }}"
                >
                    <div class="p-5">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            {{-- Label --}}
                            <div>
                                <label
                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                                    for="config-label-{{ $config->id }}"
                                >
                                    {{ __('settings::page.configuration.label') }}
                                </label>
                                <div class="text-sm text-gray-800 dark:text-white/90">
                                    {{ $config->label ?: $config->group . '.' . $config->key }}
                                </div>
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $config->group }}.{{ $config->key }}
                                </div>
                            </div>

                            {{-- Value --}}
                            <div>
                                <label
                                    class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                                    for="config-value-{{ $config->id }}"
                                >
                                    {{ __('settings::page.configuration.value') }}
                                </label>
                                <textarea
                                    class="form-input min-h-20"
                                    id="config-value-{{ $config->id }}"
                                    rows="3"
                                    wire:change="updateConfig({{ $config->id }}, $event.target.value)"
                                >{{ $config->value }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="flex flex-col items-center justify-center p-10">
                        <i class="fa-solid fa-inbox mb-4 text-6xl text-gray-300 dark:text-gray-600"></i>
                        <p class="text-center text-gray-500 dark:text-gray-400">
                            {{ __('settings::page.configuration.no_data') }}
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
                        <span>{{ __('settings::page.configuration.loading') }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
