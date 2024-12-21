<x-wireui-modal name="generateDarfsModal" width="2xl items-center sm:items-center" persistent x-on:open="$wire.generateDarfs()">
    <x-wireui-card title="{{ __('Save DARFs') }}" class="w-full md:w-2/3 lg:w-1/2 text-secondary-700 dark:text-secondary-200">

        <div class="w-full" wire:loading wire:target="generateDarfs">
            <div class="px-6 py-12 flex justify-center">
                <x-loading />
            </div>
        </div>

        <ul role="list" class="divide-y divide-gray-100 w-full" wire:loading.remove wire:target="generateDarfs">
            @forelse ($darfs as $darfKey => $darf)
                <li class="flex flex-col py-5">
                    <div class="flex justify-around items-center gap-x-6 mb-3">
                        <div class="min-w-0 flex-auto">
                            <span class="flex text-xl font-bold items-center">
                                <x-wireui-icon name="calendar" class="me-1 h-5 w-5" />
                                {{ $darf['date']->format('m/Y') }}
                            </span>
                        </div>

                        <div>
                            @if (!$errors->has('darfs.' . $darfKey))
                                <x-wireui-button xs outline primary label="{{ __('Save DARF') }}" wire:click="saveDarf('{{ $darfKey }}')" wire:loading.attr="disabled" wire:target="saveDarf('{{ $darfKey }}')">
                                    <x-slot name="prepend" class="flex center align-middle" wire:loading wire:target="saveDarf('{{ $darfKey }}')">
                                        <x-loading color="gray-200" size="4" />
                                    </x-slot>
                                </x-wireui-button>
                            @endif
                        </div>
                    </div>

                    @error('darfs.' . $darfKey)
                        <x-wireui-alert warning class="mb-2">
                            <x-slot name="title">
                                {{ $message }}
                            </x-slot>

                            <x-slot name="action">
                                <!-- ignore for now button -->
                                <x-wireui-button xs flat warning label="{{ __('Ignore for now') }}" wire:click="ignoreDarf('{{ $darfKey }}')" wire:loading.attr="disabled" wire:target="ignoreDarf('{{ $darfKey }}')">
                                    <x-slot name="prepend" class="flex center align-middle" wire:loading wire:target="ignoreDarf('{{ $darfKey }}')">
                                        <x-loading color="gray-200" size="4" />
                                    </x-slot>
                                </x-wireui-button>

                                <x-wireui-button xs warning label="{{ __('Save anyway') }}" wire:click="saveDarf('{{ $darfKey }}', true)" wire:loading.attr="disabled" wire:target="saveDarf('{{ $darfKey }}', true)">
                                    <x-slot name="prepend" class="flex center align-middle" wire:loading wire:target="saveDarf('{{ $darfKey }}', true)">
                                        <x-loading color="gray-200" size="4" />
                                    </x-slot>
                                </x-wireui-button>
                            </x-slot>
                            </x-alert>
                        @enderror

                        <div class="flex flex-wrap gap-x-4 text-center">
                            <div class="min-w-0 flex-auto">
                                <p class="text-sm/6 font-semibold">{{ __('Brazilian Stock Profit') }}</p>
                                <p class="mt-1 truncate text-xs/5 text-gray-500">@money($darf['brazilian_stock_profit'])</p>
                            </div>
                            <div class="min-w-0 flex-auto">
                                <p class="text-sm/6 font-semibold">{{ __('FII Profit') }}</p>
                                <p class="mt-1 truncate text-xs/5 text-gray-500">@money($darf['fii_profit'])</p>
                            </div>
                            <div class="min-w-0 flex-auto">
                                <p class="text-sm/6 font-semibold">{{ __('BDR and ETF Profit') }}</p>
                                <p class="mt-1 truncate text-xs/5 text-gray-500">@money($darf['bdr_and_etf_profit'])</p>
                            </div>
                            <div class="min-w-0 flex-auto">
                                <p class="text-sm/6 font-semibold">{{ __('Day trade Profit') }}</p>
                                <p class="mt-1 truncate text-xs/5 text-gray-500">@money($darf['day_trade_profit'])</p>
                            </div>
                            <div class="min-w-0 flex-auto">
                                <p class="text-sm/6 font-bold">{{ __('DARF Value') }}</p>
                                <p class="mt-1 truncate text-xs/5 text-gray-500 font-semibold">@money($darf['value'])</p>
                            </div>
                        </div>
                </li>
            @empty
                <li class="flex flex-col py-5 items-center">
                    <x-wireui-icon name="inbox" class="h-6 w-6 text-gray-400 mb-2" />
                    <span class="text-gray-500">{{ __('No DARFs to save') }}</span>

                    <small class="text-gray-500 mt-1">{{ __('It seems that all DARFs based on your stock trades have already been generated.') }}</small>
                </li>
            @endforelse
        </ul>

        <x-slot name="footer" class="flex justify-end gap-x-4">
            <x-wireui-button flat :label="__('Cancel')" x-on:click="close; $wire.resetAll()" />

            <x-wireui-button primary :label="__('Save all')" wire:click="saveAll" wire:loading.attr="disabled" wire:target="saveAll">
                <x-slot name="prepend" class="flex center align-middle" wire:loading wire:target="saveAll">
                    <x-loading color="gray-200" size="4" />
                </x-slot>
            </x-wireui-button>
        </x-slot>
    </x-wireui-card>
</x-wireui-modal>
