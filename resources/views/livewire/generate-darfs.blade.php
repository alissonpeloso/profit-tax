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
        @foreach ($darfs as $darf)
            <div class="flex justify-between gap-x-4">
                <div class="collapse collapse-arrow border border-secondary-700 dark:border-secondary-400">
                    <input type="radio" name="my-accordion-1" checked="checked" />
                    <div class="collapse-title text-xl font-medium">
                        <div class="flex min-w-0 gap-x-4">
                            <div class="min-w-0 flex-auto">
                                <p class="text-md/6 font-semibold">{{ $darf->date->format('m/y') }} - @money($darf->value)</p>
                                <p class="mt-1 truncate text-xs/5 text-gray-500">leslie.alexander@example.com</p>
                            </div>
                            @if ($darf->due_date)
                                <div class="min-w-0 flex-auto">
                                    @if (now()->isBefore($darf->due_date))
                                        <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">
                                            {{ __('Delayed') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-700 ring-1 ring-inset ring-yellow-600/10">
                                            {{ __('To Pay') }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="collapse-content">
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
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
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
