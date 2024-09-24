<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Broker;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use App\Services\BrokerageService;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;

class UploadBrokerageNote extends Component
{
    use WithFileUploads;

    #[Validate([
        'brokerageNotes.*' => 'required|file|mimes:pdf',
    ], message: [
        'brokerageNotes.*.required' => 'The brokerage note is required.',
        'brokerageNotes.*.file' => 'The brokerage note must be a file.',
        'brokerageNotes.*.mimes' => 'The brokerage note must be a file of type: pdf.',
    ])]
    public array $brokerageNotes = [];

    #[Validate([
        'selectedBrokers.*' => 'required|exists:brokers,id',
    ], [
        'selectedBrokers.*.required' => 'The broker is required.',
        'selectedBrokers.*.exists' => 'The selected broker is invalid.',
    ])]
    public array $selectedBrokers = [];

    public function render(): View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        return view('livewire.upload-brokerage-note', [
            'brokers' => $this->getBrokersProperty(),
        ]);
    }

    protected function getBrokersProperty(): Collection
    {
        return Broker::select('id', 'name')->get();
    }

    public function removeBrokerageNote($index): void
    {
        unset($this->brokerageNotes[$index]);
    }

    public function extract(): void
    {
        $this->validate();
        $firstBrokerIdAvailable = $this->getBrokersProperty()->first()->id;

        /** @var BrokerageService $brokerageService */
        $brokerageService = app()->make(BrokerageService::class);

        /** @var User $user */
        $user = auth()->user();

        foreach ($this->brokerageNotes as $index => $brokerageNote) {
            $brokerageService->extract($brokerageNote, $this->selectedBrokers[$index] ?? $firstBrokerIdAvailable, $user);
        }
    }
}
