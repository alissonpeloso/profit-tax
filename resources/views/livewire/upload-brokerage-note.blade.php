<x-card title="{{ __('Upload Brokerage Note') }}" class="w-full md:w-2/3 lg:w-1/2">
    <p class="text-center text-secondary-500 mb-2">
        {{ __('Upload your brokerage note here. You can upload multiple files at once.') }}
    </p>

    <label
        class="flex items-center justify-center col-span-1 bg-gray-100 shadow-md cursor-pointer md:col-span-2 dark:bg-secondary-700 rounded-xl h-64">
        <div class="flex flex-col items-center justify-center" wire:loading.remove wire:target="brokerageNotes">
            <x-icon name="cloud-arrow-up" class="w-16 h-16 text-primary-400" />

            <p class="text-primary-400">Click or drop files here</p>
            <input type="file" class="hidden" wire:model="brokerageNotes" multiple />
        </div>

        <div class="flex flex-col items-center justify-center" wire:loading wire:target="brokerageNotes">
            <x-loading />
        </div>
    </label>

    @error('brokerageNotes')
    <p class="text-sm text-red-500">{{ $message }}</p>
    @enderror

    @if ($brokerageNotes)
        <div class="col-span-1 md:col-span-2">
            <ul class="grid grid-cols-1 gap-4 mt-4">
                @foreach ($brokerageNotes as $key => $brokerageNote)
                    <li class="flex items center justify-between p-4 bg-gray-100 shadow-md dark:bg-secondary-700 rounded-xl">
                        <div class="flex items center">
                            <x-icon name="document-text" class="w-8 h-8 text-primary-400" />
                            <p class="ml-4 self-center">{{ $brokerageNote->getClientOriginalName() }}</p>
                        </div>

                        <button wire:click="removeBrokerageNote({{ $key }})"
                                class="ml-4 text-red-500 hover:text-red-700 float-end">
                            <x-icon name="trash" class="w-6 h-6" />
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

</x-card>
