<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h2
        class="text-xl font-semibold text-gray-800 dark:text-white/90"
        x-text="pageName"
    ></h2>

    <nav>
        <ol class="flex items-center gap-1.5">
            <li>
                <a
                    class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                    href="{{ backend_route('home.dashboard') }}"
                    wire:navigate
                >
                    {{ __('home::menu.be.index') }}
                    @if (isset($breadcrumbs) && $breadcrumbs->hasItems())
                        <svg
                            class="stroke-current"
                            width="17"
                            height="16"
                            viewBox="0 0 17 16"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366"
                                stroke=""
                                stroke-width="1.2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    @endif
                </a>
            </li>
            @if (isset($breadcrumbs) && $breadcrumbs->hasItems())
                @php
                    $i = 0;
                    $count = $breadcrumbs->count();
                @endphp
                @foreach ($breadcrumbs->items() as $breadcrumb)
                    @php $i++; @endphp
                    <li class="text-sm text-gray-800 dark:text-white/90">
                        @if (!empty($breadcrumb['icon']))
                            <span class="{{ $breadcrumb['icon'] }}"></span>
                        @endif
                        <a
                            class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                            href="{{ $breadcrumb['url'] }}"
                            title="{{ $breadcrumb['label'] }}"
                            wire:navigate
                        >
                            {{ $breadcrumb['label'] }}
                            @if ($i != $count)
                                <svg
                                    class="stroke-current"
                                    width="17"
                                    height="16"
                                    viewBox="0 0 17 16"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366"
                                        stroke=""
                                        stroke-width="1.2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            @endif
                        </a>
                    </li>
                @endforeach
            @endif
        </ol>
    </nav>
</div>
