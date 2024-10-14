<div class="fixed right-5 bottom-5">
    <x-wireui-dropdown width="100">
        <x-slot name="trigger">
            <x-wireui-button lg rounded primary label="Quick Actions" icon="plus"></x-wireui-button>
        </x-slot>

        <x-wireui-dropdown.item label="{{ __('Extract from a Brokerage Note') }} " icon="document-text"
            x-on:click="$openModal('uploadBrokerageNoteModal')" />

        <x-wireui-dropdown.item label="{{ __('Generate Darfs') }}" icon="arrow-trending-up"
            x-on:click="$openModal('generateDarfsModal')" />
    </x-wireui-dropdown>
</div>

<x-wireui-modal name="uploadBrokerageNoteModal" width="2xl items-center sm:items-center" persistent
    x-on:close-modal="$closeModal('uploadBrokerageNoteModal')">
    <livewire:upload-brokerage-note />
</x-wireui-modal>

<x-wireui-modal name="generateDarfsModal" width="2xl items-center sm:items-center" persistent
    x-on:close-modal="$closeModal('generateDarfsModal')">
    <livewire:generate-darfs />
</x-wireui-modal>
