<x-wireui-card title="{{ __('Upload Brokerage Note') }}" class="w-full md:w-2/3 lg:w-1/2">
    @if (empty($darfs))
        <div class="flex justify-center gap-y-4">
            <div class="py-5">
                <x-wireui-button primary :label="__('Generate Darfs')" wire:click="generateDarfs" wire:loading.attr="disabled"
                    wire:target="generateDarfs">
                    <x-slot name="prepend" class="flex center align-middle" wire:loading wire:target="generateDarfs">
                        <x-loading color="gray-200" size="4" />
                    </x-slot>
                </x-wireui-button>
            </div>
        </div>
    @else
        @foreach ($darfs as $darf)
            <div class="flex justify-between gap-x-4">
                <div class="collapse collapse-arrow bg-gray-700">
                    <input type="radio" name="my-accordion-2" checked="checked" />
                    <div class="collapse-title text-xl font-medium">
                        {{ $darf->date->format('m/y') }} - {{ $darf->value }}
                    </div>
                    <div class="collapse-content">
                        <p>hello</p>
                    </div>
                </div>
            </div>
        @endforeach
    @endif


    <x-slot name="footer" class="flex justify-end gap-x-4">
        <x-wireui-button flat :label="__('Cancel')" x-on:click="close" />

        <x-wireui-button primary :label="__('Extract')" wire:click="extract" wire:loading.attr="disabled"
            wire:target="extract">
            <x-slot name="prepend" class="flex center align-middle" wire:loading wire:target="extract">
                <x-loading color="gray-200" size="4" />
            </x-slot>
        </x-wireui-button>
    </x-slot>
</x-wireui-card>
