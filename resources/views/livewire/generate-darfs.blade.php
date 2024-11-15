<x-wireui-card title="{{ __('Save DARFs') }}" class="w-full md:w-2/3 lg:w-1/2 text-secondary-700 dark:text-secondary-200">
    @if (empty($darfs))
        <div class="flex justify-center gap-y-4">
            <div class="py-5">
                <x-wireui-button primary :label="__('Generate Darfs')" wire:click="generateDarfs" wire:loading.attr="disabled" wire:target="generateDarfs">
                    <x-slot name="prepend" class="flex center align-middle" wire:loading wire:target="generateDarfs">
                        <x-loading color="gray-200" size="4" />
                    </x-slot>
                </x-wireui-button>
            </div>
        </div>
    @else
        <ul role="list" class="divide-y divide-gray-100">
            @foreach ($darfs as $darfKey => $darf)
                <li class="flex flex-col py-5">
                    <div class="flex justify-around items-center gap-x-6 mb-3">
                        <div class="min-w-0 flex-auto">
                            <span class="flex text-xl font-bold items-center">
                                <x-wireui-icon name="calendar" class="me-1 h-5 w-5" />
                                {{ $darf->date->format('m/Y') }}
                            </span>
                        </div>

                        <div>
                            <x-wireui-button xs outline primary label="{{ __('Save DARF') }}" wire:click="saveDarf('{{ $darfKey }}')" wire:loading.attr="disabled" wire:target="saveDarf('{{ $darfKey }}')">
                                <x-slot name="prepend" class="flex center align-middle" wire:loading wire:target="saveDarf('{{ $darfKey }}')">
                                    <x-loading color="gray-200" size="4" />
                                </x-slot>
                            </x-wireui-button>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-x-4 text-center">
                        <div class="min-w-0 flex-auto">
                            <p class="text-sm/6 font-semibold">{{ __('Brazilian Stock Profit') }}</p>
                            <p class="mt-1 truncate text-xs/5 text-gray-500">@money($darf->brazilian_stock_profit)</p>
                        </div>
                        <div class="min-w-0 flex-auto">
                            <p class="text-sm/6 font-semibold">{{ __('FII Profit') }}</p>
                            <p class="mt-1 truncate text-xs/5 text-gray-500">@money($darf->fii_profit)</p>
                        </div>
                        <div class="min-w-0 flex-auto">
                            <p class="text-sm/6 font-semibold">{{ __('BDR and ETF Profit') }}</p>
                            <p class="mt-1 truncate text-xs/5 text-gray-500">@money($darf->bdr_and_etf_profit)</p>
                        </div>
                        <div class="min-w-0 flex-auto">
                            <p class="text-sm/6 font-semibold">{{ __('Day trade Profit') }}</p>
                            <p class="mt-1 truncate text-xs/5 text-gray-500">@money($darf->day_trade_profit)</p>
                        </div>
                        <div class="min-w-0 flex-auto">
                            <p class="text-sm/6 font-bold">{{ __('DARF Value') }}</p>
                            <p class="mt-1 truncate text-xs/5 text-gray-500 font-semibold">@money($darf->value)</p>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    <x-slot name="footer" class="flex justify-end gap-x-4">
        <x-wireui-button flat :label="__('Cancel')" x-on:click="close" />

        <x-wireui-button primary :label="__('Save all')" wire:click="saveAll" wire:loading.attr="disabled" wire:target="saveAll">
            <x-slot name="prepend" class="flex center align-middle" wire:loading wire:target="saveAll">
                <x-loading color="gray-200" size="4" />
            </x-slot>
        </x-wireui-button>
    </x-slot>
</x-wireui-card>
