<div class="w-full" x-data="{ isCreating: @entangle('isCreating') }">
    @php
        $groupedStockTrades = $this->stockTrades()->groupBy('date');
    @endphp

    <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 flex justify-between">
        <div>
            <label for="perPage" class="text-sm text-gray-900 dark:text-gray-200">{{ __('Page Size') }}</label>
            <select wire:model.live="perPage" id="perPage" class="block w-24 mt-1 form-select shadow-sm sm:text-sm rounded-md dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200">
                @foreach (App\Livewire\StockTradeList::PAGE_SIZES as $size)
                    <option value="{{ $size }}">{{ $size }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-end gap-2">
            <div x-show="!isCreating" x-transition>
                <x-wireui-button primary light label="{{ __('Create new') }}" icon="plus" lg @click="isCreating = true" />
            </div>

            <div>
                <label for="search" class="text-sm text-gray-900 dark:text-gray-200">{{ __('Search') }}</label>
                <input wire:model.live.debounce="search" wire:loading.attr="disabled" id="search" type="text" class="block w-60 mt-1 form-input shadow-sm sm:text-sm rounded-md dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200">
            </div>
        </div>
    </div>

    <div wire:loading wire:target.except="delete,editingStockTradeId,isCreating" class="w-full">
        <div class="px-6 py-12 flex justify-center">
            <x-loading />
        </div>
    </div>

    <div x-show="isCreating" x-transition>
        <div class="px-4 pt-8 rounded relative text-neutral-300 text-center" role="alert">
            <livewire:stock-trade-form @cancel="isCreating = false"
                @saved="isCreating = false; $dispatch('refresh-stock-trade-list')"
            />
        </div>
    </div>

    <div class="w-full pb-12 pt-4" wire:loading.remove wire:target.except="delete,editingStockTradeId,isCreating">
        @if (empty($this->stockTrades()->items()))
            <div class="px-4 pt-8 rounded relative text-neutral-300 text-center" role="alert">
                <span class="block sm:inline">{{ __('No stock trades found') }}</span>
            </div>
        @else
            <div class="w-full mx-auto sm:px-6 lg:px-8">
                @foreach ($groupedStockTrades as $date => $dateGroup)
                    <h3 class="px-6 py-3 text-lg font-semibold text-gray-900 dark:text-gray-200 mt-3">
                        {{ \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') }}
                    </h3>

                    <div class="bg-white dark:bg-gray-800 overflow-auto shadow-xl sm:rounded-lg">
                        <table
                            class="table-auto min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Symbol') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Operation') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Quantity') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Price') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Total') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Fee') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('IR') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Broker') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Note Identifier') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Class') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Action') }}
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($dateGroup as $stockTrade)
                                @if ($editingStockTradeId === $stockTrade->id)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap" colspan="10">
                                            <livewire:stock-trade-form
                                                :stockTrade="$stockTrade"
                                                :key="$stockTrade->id"
                                                @cancel="set('editingStockTradeId', null)"
                                                @saved="set('editingStockTradeId', null)"
                                            />
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class=" px-6 py-4 whitespace-nowrap
                                            ">
                                            <div class="text-sm text-gray-900 dark:text-gray-200">
                                                {{ $stockTrade->stock_symbol }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-200">
                                                {{ __(\App\Models\StockTrade::OPERATIONS[$stockTrade->operation]) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-200">
                                                {{ $stockTrade->quantity }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-200">
                                                @money($stockTrade->price)
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-200">
                                                @money($stockTrade->quantity * $stockTrade->price)
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-200">
                                                @money($stockTrade->fee)
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-200">
                                                @money($stockTrade->ir)
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-200">
                                                {{ $stockTrade->broker->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-200">
                                                {{ $stockTrade->note_id }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-200">
                                                {{ __(\App\Models\StockTrade::CLASSES[$stockTrade->class] ?? null) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-wireui-mini-button outline primary icon="pencil-square" wire:click="$set('editingStockTradeId', {{ $stockTrade->id }})" />

                                            <x-wireui-mini-button outline red icon="trash" wire:click="delete({{ $stockTrade->id }})" wire:confirm="{{ __('Are you sure you want to delete this stock trade?') }}" />
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach

                @if ($this->stockTrades()->hasPages())
                    <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3">
                        {{ $this->stockTrades()->links() }}
                    </div>
                @endif
            </div> @endif </div>
        </div>
