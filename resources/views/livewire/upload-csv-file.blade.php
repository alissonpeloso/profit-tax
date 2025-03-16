<x-wireui-modal name="uploadCsvFileModal" width="items-center sm:items-center" persistent x-on:close-modal="$closeModal('uploadCsvFileModal')">
    <x-wireui-card title="{{ __('Upload CSV File') }}" class="w-full lg:w-4/5 xl:w-2/3 2xl:w-1/2">
        <p class="text-center text-secondary-500 mb-2">
            {{ __('Upload your CSV file here and link the columns to the fields in the database.') }}
        </p>

        <label class="flex items-center justify-center col-span-1 bg-gray-100 shadow-md cursor-pointer md:col-span-2 dark:bg-secondary-700 rounded-xl h-64 @error('file') border border-red-500 @enderror">
            <div class=" flex flex-col items-center justify-center" wire:loading.class="hidden" wire:target="file">
                <x-wireui-icon name="cloud-arrow-up" class="w-16 h-16 text-primary-400" />

                <p class="text-primary-400">{{ __('Click or drop files here') }}</p>
                <input type="file" class="hidden" wire:model.live="file" accept=".csv" />
            </div>

            <div class="flex flex-col items-center justify-center" wire:loading wire:target="file">
                <x-loading />
            </div>
        </label>

        @error('file')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500 text-center">
                <span class="font-medium">{{ $message }}</span>
            </p>
        @enderror

        @if ($file)
            <hr class="col-span-1 md:col-span-2 my-4 border-gray-300 dark:border-gray-700" />

            <div class="col-span-1 md:col-span-2" wire:loading.class="hidden" wire:target="extract">
                <ul class="grid grid-cols-1 gap-4 mt-4 max-h-60 overflow-y-auto">
                    <li class="flex flex-col p-4 bg-gray-100 shadow-md dark:bg-secondary-700 rounded-xl @error('file') border border-red-500 pb-0 @enderror">
                        <div class="flex items center justify-between">
                            <div class="flex items center">
                                <x-wireui-icon name="document-text" class="w-8 h-8 text-primary-400" />
                                <p class="ml-4 self-center">{{ $file->getClientOriginalName() }}</p>
                                <p class="ml-4 self-center text-sm text-gray-500 text-center">
                                    {{ round($file->getSize() / 1024, 2) }} KB
                                </p>
                            </div>
                        </div>

                        @error('file')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-500 text-center">
                                <span class="font-medium">{{ $message }}</span>
                            </p>
                        @enderror
                    </li>
                </ul>
            </div>

            <div class="col-span-1 md:col-span-2 mt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach ($columns as $attribute => $rule)
                        <div>
                            <x-wireui-select label="{{ $rule }}" placeholder="{{ __('Select a column') }}" wire:model.live="{{ $attribute }}" :options="$csvHeaders" />
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-span-1 md:col-span-2 flex items-center justify-center my-4 hidden" wire:loading.class.remove="hidden" wire:target="extract">
                <x-loading size="8" />
            </div>
        @endif

        <x-slot name="footer" class="flex justify-end gap-x-4">
            <x-wireui-button flat :label="__('Cancel')" x-on:click="close" />

            <x-wireui-button primary :label="__('Extract')" wire:click="extract" wire:loading.attr="disabled" wire:target="extract">
                <x-slot name="prepend" class="flex center align-middle" wire:loading wire:target="extract">
                    <x-loading color="gray-200" size="4" />
                </x-slot>
            </x-wireui-button>
        </x-slot>
    </x-wireui-card>
</x-wireui-modal>
