<x-card title="{{ __('Upload Brokerage Note') }}" class="w-full md:w-2/3 lg:w-1/2">
    <p class="text-center text-secondary-500 mb-2">
        {{ __('Upload your brokerage note here. You can upload multiple files at once.') }}
    </p>

    <label
        class="flex items-center justify-center col-span-1 bg-gray-100 shadow-md cursor-pointer md:col-span-2 dark:bg-secondary-700 rounded-xl h-64">
        <div class="flex flex-col items-center justify-center" wire:loading.remove wire:target="brokerageNotes">
            <x-icon name="cloud-arrow-up" class="w-16 h-16 text-primary-400" />

            <p class="text-primary-400">Click or drop files here</p>
            <input type="file" class="hidden" wire:model="brokerageNotes" wire:model.live multiple />
        </div>

        <div class="flex flex-col items-center justify-center" wire:loading wire:target="brokerageNotes">
            <x-loading />
        </div>
    </label>

    @error('brokerageNotes.*')
    <p class="text-sm text-red-500">{{ $message }}</p>
    @enderror

    {{ $var_dump(brokerageNotes) }}

    @if ($brokerageNotes)
        <div class="col-span-1 md:col-span-2">
            <ul class="grid grid-cols-1 gap-4 mt-4">
                @foreach ($brokerageNotes as $key => $brokerageNote)
                    <li class="flex items center justify-between p-4 bg-gray-100 shadow-md dark:bg-secondary-700 rounded-xl">
                        <div class="flex items center">
                            <x-icon name="document-text" class="w-8 h-8 text-primary-400" />
                            <p class="ml-4 self-center">{{ $brokerageNote->getClientOriginalName() }}</p>
                            <p class="ml-4 self-center text-sm text-gray-500">
                                {{ $brokerageNote->broker ??'No broker selected' }}
                            </p>
                        </div>

                        <div class="col-span-2 flex">
                            <select wire:model="brokerageNotes.{{ $key }}.broker"
                                    class="p-2 text-sm bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-400 focus:border-primary-400 dark:bg-secondary-800 dark:text-gray-300 dark:border-gray-700">
                                @foreach ($brokers as $broker)
                                    <option value="{{ $broker->id }}">{{ $broker->name }}</option>
                                @endforeach
                            </select>

                            <button wire:click="removeBrokerageNote({{ $key }})"
                                    class="ml-4 text-red-500 hover:text-red-700 float-end">
                                <x-icon name="trash" class="w-6 h-6" />
                            </button>
                        </div>

                    </li>
                @endforeach
            </ul>
        </div>
    @endif

</x-card>
