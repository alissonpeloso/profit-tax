@use('App\Enum\DarfStatus')

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

        <div class="flex items-end gap-2">
            <x-wireui-button primary light label="{{ __('Generate Darfs') }}" icon="arrow-trending-up" lg x-on:click="$openModal('generateDarfsModal')" />

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
        @if (empty($darfs->items()))
            <div class="px-4 pt-8 rounded relative text-neutral-300 text-center" role="alert">
                <span class="block sm:inline">{{ __('No DARFs found') }}</span>
            </div>
        @else
            <div class="w-full mx-auto sm:px-6 lg:px-8">
                @foreach ($darfs->items() as $darf)
                    <div class="bg-white dark:bg-gray-800 sm:rounded-lg mb-2 overflow-visible" x-data="{ open: false }" key='darf-{{ $darf->id }}' wire:key="darf-{{ $darf->id }}">
                        <div class="px-4 py-5 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="text-lg font-medium text-gray-900 dark:text-gray-200 flex flex-col">
                                    <span>{{ $darf->date->format('m/Y') }}</span>

                                    <span class="text-md text-gray-500 dark:text-gray-400">
                                        @money($darf->value)
                                    </span>
                                </div>
                                <div class="flex-shrink-0 flex">
                                    <span role="button" class="flex justify-center">
                                        @php
                                            $darfStatus = DarfStatus::from($darf->status);
                                        @endphp

                                        <x-wireui-badge rounded="full" md flat color="{{ $darfStatus->getColor() }}" class="ms-3">
                                            {{ $darfStatus->getLabel() }}
                                        </x-wireui-badge>
                                    </span>

                                    {{-- Button to edit the Status of the Darf --}}
                                    <x-wireui-dropdown>
                                        <x-slot name="trigger">
                                            <x-wireui-mini-button title="{{ __('Edit DARF status') }}" rounded icon="pencil" flat gray class="ms-3" />
                                        </x-slot>

                                        @foreach (DarfStatus::cases() as $status)
                                            @if ($darf->status !== $status->value)
                                                <x-wireui-dropdown.item wire:click="editStatus({{ $darf->id }}, '{{ $status->value }}')" label="{{ $status->getLabel() }}" />
                                            @endif
                                        @endforeach
                                    </x-wireui-dropdown>

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
                                                $darf->status === DarfStatus::PENDING->value,
                                        ])>
                                            <x-wireui-icon name="calendar" class="w-4 h-4 mr-1" />
                                            {{ $darf->due_date->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if ($darfs->hasPages())
                    <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3">
                        {{ $darfs->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <div>
        <livewire:generate-darfs wire:key="generateDarfsModal" />
    </div>
</div>
