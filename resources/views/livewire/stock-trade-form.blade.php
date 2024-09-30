<div class="flex flex-col">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">
        {{ $title }}
    </h2>

    <div class="w-full grid grid-cols-3 lg:grid-cols-4 gap-3">
        <div>
            <x-wireui-datetime-picker
                label="{{ __('Date') }}"
                placeholder="{{ __('Select a Date') }}"
                :without-time="true"
                :clearable="false"
                timezone="America/Sao_Paulo"
                wire:model="date"
            />
        </div>

        <div>
            <x-wireui-input
                label="{{ __('Stock Symbol') }}"
                type="text"
                wire:model="stockSymbol"
            />
        </div>

        <div>
            <x-wireui-select
                label="{{ __('Operation') }}"
                placeholder="Select an Operation"
                :clearable="false"
                wire:model="operation"
            >
                @foreach(\App\Models\StockTrade::OPERATIONS as $key => $operation)
                    <x-wireui-select.option value="{{ $key }}">{{ __($operation) }}</x-wireui-select.option>
                @endforeach
            </x-wireui-select>
        </div>

        <div>
            <x-wireui-input
                label="{{ __('Quantity') }}"
                type="number"
                wire:model="quantity"
            />
        </div>

        <div>
            <x-wireui-currency
                label="{{ __('Price') }}"
                prefix="R$"
                thousands="."
                decimal=","
                wire:model="price"
            />
        </div>

        <div>
            <x-wireui-currency
                label="{{ __('Fee') }}"
                prefix="R$"
                thousands="."
                decimal=","
                wire:model="fee"
            />
        </div>

        <div>
            <x-wireui-currency
                label="{{ __('IR') }}"
                prefix="R$"
                thousands="."
                decimal=","
                wire:model="ir"
            />
        </div>

        <div>
            <x-wireui-select
                label="Search a Broker"
                placeholder="Select a Broker"
                :clearable="false"
                :async-data="[
                'api' => route('brokers.search'),
                'credential' => csrf_token(),
            ]"
                option-label="name"
                option-value="id"
                wire:model="brokerId"
                :value="$brokerId"
            />
        </div>

        <div>
            <x-wireui-input
                label="{{ __('Note Identifier') }}"
                type="text"
                wire:model="noteId"
            />
        </div>

        <div>
            <x-wireui-select
                label="{{ __('Class') }}"
                placeholder="Select a Class"
                :clearable="false"
                wire:model="class"
            >
                @foreach(\App\Models\StockTrade::CLASSES as $key => $class)
                    <x-wireui-select.option value="{{ $key }}">{{ __($class) }}</x-wireui-select.option>
                @endforeach
            </x-wireui-select>
        </div>

        <div class="flex flex-col gap-3">
            <x-wireui-toggle
                label="{{ __('Is Day Trade?') }}"
                wire:model="isDayTrade"
                lg
            />

            <x-wireui-toggle
                label="{{ __('Is Exempt?') }}"
                wire:model="isExempt"
                lg
            />
        </div>

        <div class="col-span-3 lg:col-span-4">
            <div class="flex justify-end gap-1">
                <x-wireui-button
                    flat
                    secondary
                    wire:click="cancel"
                    wire:loading.attr="disabled"
                    wire:target="cancel"
                >
                    {{ __('Cancel') }}
                </x-wireui-button>

                <x-wireui-button
                    class="mr-2"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    wire:target="save"
                >
                    <x-slot name="prepend" class="flex items-center" wire:loading wire:target="save">
                        <x-loading size="4" color="gray-200" />
                    </x-slot>
                    {{ __('Save') }}
                </x-wireui-button>
            </div>
        </div>
    </div>
</div>
