<div class="w-full">
    <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 flex justify-between">
        <div>
            <label for="perPage" class="text-sm text-gray-900 dark:text-gray-200">{{ __('Page Size') }}</label>
            <select wire:model.live="perPage" id="perPage" class="block w-24 mt-1 form-select shadow-sm sm:text-sm rounded-md dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200">
                @foreach (App\Livewire\DarfsList::PAGE_SIZES as $size)
                    <option value="{{ $size }}">{{ $size }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-end">
            <div>
                <label for="search" class="text-sm text-gray-900 dark:text-gray-200">{{ __('Search') }}</label>
                <input wire:model.live.debounce="search" wire:loading.attr="disabled" id="search" type="search" class="block w-60 mt-1 form-input shadow-sm sm:text-sm rounded-md dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200">
            </div>
        </div>
    </div>

    <div wire:loading class="w-full">
        <div class="px-6 py-12 flex justify-center">
            <x-loading />
        </div>
    </div>

    <div class="w-full py-4" wire:loading.remove>
        @if (empty($this->darfs()->items()))
            <div class="px-4 pt-8 rounded relative text-neutral-300 text-center" role="alert">
                <span class="block sm:inline">{{ __('No DARFs found') }}</span>
            </div>
        @else
            <div class="w-full mx-auto sm:px-6 lg:px-8">
                @foreach ($this->darfs()->items() as $darf)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg mb-2" x-data="{ open: false }">
                        <div class="px-4 py-5 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="text-lg font-medium text-gray-900 dark:text-gray-200 flex flex-col">
                                    <span>{{ $darf->date->format('m/Y') }}</span>

                                    <span class="text-md text-gray-500 dark:text-gray-400">
                                        @money($darf->value)
                                    </span>
                                </div>
                                <div class="flex-shrink-0 flex">
                                    <span role="button">
                                        @switch($darf->status)
                                            @case(\App\Models\Darf::STATUS_PENDING)
                                                <x-wireui-badge rounded="full" md flat warning class="ms-3">{{ __('Pending') }}</x-wireui-badge>
                                            @break

                                            @case(\App\Models\Darf::STATUS_PAID)
                                                <x-wireui-badge rounded="full" md flat success class="ms-3">{{ __('Paid') }}</x-wireui-badge>
                                            @break

                                            @case(\App\Models\Darf::STATUS_CANCELED)
                                                <x-wireui-badge rounded="full" md flat danger class="ms-3">{{ __('Canceled') }}</x-wireui-badge>
                                            @break

                                            @default
                                        @endswitch
                                    </span>

                                    <div title="{{ __('show/hide details') }}" @click="open = !open" class="cursor-pointer">
                                        <template x-if="open">
                                            <x-wireui-mini-button rounded icon="chevron-up" flat gray class="ms-3" />
                                        </template>

                                        <template x-if="!open">
                                            <x-wireui-mini-button rounded icon="chevron-down" flat gray class="ms-3" />
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div x-show="open" x-transition>
                                <hr class="mt-3 border-t border-gray-200 dark:border-gray-600">

                                <div class="mt-3 flex items-center text-center text-gray-900 dark:text-gray-200">
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold">{{ __('Brazilian Stock Profit') }}</p>
                                        <p class="mt-1 truncate text-md text-gray-500 dark:text-gray-400">@money($darf->brazilian_stock_profit)</p>
                                    </div>
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold">{{ __('FII Profit') }}</p>
                                        <p class="mt-1 truncate text-md text-gray-500 dark:text-gray-400">@money($darf->fii_profit)</p>
                                    </div>
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold">{{ __('BDR and ETF Profit') }}</p>
                                        <p class="mt-1 truncate text-md text-gray-500 dark:text-gray-400">@money($darf->bdr_and_etf_profit)</p>
                                    </div>
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold">{{ __('Day trade Profit') }}</p>
                                        <p class="mt-1 truncate text-md text-gray-500 dark:text-gray-400">@money($darf->day_trade_profit)</p>
                                    </div>
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-md font-semibold">{{ __('Due Date') }}</p>
                                        <p @class([
                                            'mt-1 truncate text-md text-gray-500 dark:text-gray-400 flex items-center justify-center',
                                            'text-red-500 dark:text-red-400' =>
                                                $darf->due_date->isPast() &&
                                                $darf->status === \App\Models\Darf::STATUS_PENDING,
                                        ])>
                                            <x-wireui-icon name="calendar" class="w-4 h-4 mr-1" />
                                            {{ $darf->due_date->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                @endforeach

                @if ($this->darfs()->hasPages())
                    <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3">
                        {{ $this->darfs()->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
