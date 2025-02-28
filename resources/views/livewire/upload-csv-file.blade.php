<x-wireui-modal name="uploadCsvFileModal" width="items-center sm:items-center" persistent x-on:close-modal="$closeModal('uploadCsvFileModal')">
    <x-wireui-card title="{{ __('Upload CSV File') }}" class="w-full lg:w-4/5 xl:w-2/3 2xl:w-1/2">
        <p class="text-center text-secondary-500 mb-2">
            {{ __('Upload your CSV file here and link the columns to the fields in the database.') }}
        </p>


    </x-wireui-card>
</x-wireui-modal>
