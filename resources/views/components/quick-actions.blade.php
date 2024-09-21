<div class="fixed right-5 bottom-5">
    <x-dropdown width="100">
        <x-slot name="trigger">
            <x-button lg rounded info label="Quick Actions" icon="plus"></x-button>
        </x-slot>

        <x-dropdown.item label="{{ __('Extract from a Brokerage Note') }} " icon="document-text"
                         x-on:click="$openModal('uploadBrokerageNoteModal')" />
    </x-dropdown>
</div>

<x-modal name="uploadBrokerageNoteModal" width="2xl items-center sm:items-center" persistent>
    <livewire:upload-brokerage-note />
</x-modal>
