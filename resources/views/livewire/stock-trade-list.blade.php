@use('App\Enum\StockTradeOperation')
@use('App\Enum\StockTradeClass')

<div class="w-full" x-data="{ isCreating: false, editingStockTradeId: $wire.entangle('editingStockTradeId').live }">
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
            <x-wireui-dropdown>
                <x-slot name="trigger">
                    <x-wireui-button secondary light label="{{ __('Extract from') }}" lg icon="document-text" />
                </x-slot>

                <x-wireui-dropdown.item x-on:click="$openModal('uploadBrokerageNoteModal')">
                    <x-wireui-icon name="document-currency-dollar" class="w-5 h-5 mr-2" />
                    {{ __('Brokerage note') }}
                </x-wireui-dropdown.item>

                <x-wireui-dropdown.item x-on:click="$openModal('uploadCsvFileModal')">
                    <x-wireui-icon name="clipboard-document-list" class="w-5 h-5 mr-2" />
                    {{ __('CSV file') }}
                </x-wireui-dropdown.item>
            </x-wireui-dropdown>

            <div x-show="!isCreating" x-transition>
                <x-wireui-button primary light label="{{ __('Create new') }}" icon="plus" lg @click="isCreating = true" />
            </div>

            <div>
                <label for="search" class="text-sm text-gray-900 dark:text-gray-200">{{ __('Search') }}</label>
                <input wire:model.live.debounce="search" wire:loading.attr="disabled" id="search" type="text" class="block w-60 mt-1 form-input shadow-sm sm:text-sm rounded-md dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200">
            </div>
        </div>
    </div>

    <div wire:loading wire:target.except="delete,editingStockTradeId" class="w-full">
        <div class="px-6 py-12 flex justify-center">
            <x-loading />
        </div>
    </div>

    <div x-show="isCreating" x-transition x-on:cancel="isCreating=false" x-on:saved="isCreating=false; $wire.invalidateCache()">
        <div class="px-4 pt-8 rounded relative text-neutral-300 text-center" role="alert">
            <livewire:stock-trade-form key="creating" />
        </div>
    </div>

    <div class="w-full pb-12 pt-4" wire:loading.remove wire:target.except="delete,editingStockTradeId">
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

                    <div @class([
                        'bg-white dark:bg-gray-800 overflow-auto shadow-xl sm:rounded-lg soft-scrollbar',
                        'overflow-visible' => $editingStockTradeId,
                    ])>
                        <table class="table-auto min-w-full divide-y divide-gray-200 dark:divide-gray-700">
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
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" x-on:saved="editingStockTradeId = null; $wire.invalidateCache()" x-on:cancel="editingStockTradeId = null">
                                @foreach ($dateGroup as $stockTrade)
                                    @if ($editingStockTradeId === $stockTrade->id)
                                        <tr wire:key='editing-{{ $stockTrade->id }}'>
                                            <td class="px-6 py-4 whitespace-nowrap" colspan="11">
                                                <livewire:stock-trade-form :stockTrade="$stockTrade" :key="$stockTrade->id" />
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-200">
                                                    {{ $stockTrade->stock_symbol }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-200">
                                                    {{ StockTradeOperation::from($stockTrade->operation)->getLabel() }}
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
                                                    {{ StockTradeClass::tryFrom($stockTrade->class)?->getLabel() ?? null }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <x-wireui-mini-button outline primary icon="pencil-square" x-on:click="editingStockTradeId = {{ $stockTrade->id }}" />

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
            </div>
        @endif
    </div>

    <livewire:upload-brokerage-note />

    <livewire:upload-csv-file />
</div>
