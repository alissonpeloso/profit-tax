<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Broker;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use App\Services\BrokerageService;
use Illuminate\Contracts\View\View;

class UploadBrokerageNote extends Component
{
    use WithFileUploads;

    #[Validate([
        'brokerageNotes.*' => 'required|file|mimes:pdf',
    ])]
    public array $brokerageNotes = [];

    #[Validate([
        'selectedBrokers.*' => 'required|exists:brokers,id',
    ])]
    public array $selectedBrokers = [];

    public function render(): View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        return view('livewire.upload-brokerage-note', [
            'brokers' => Broker::select('id', 'name')->get(),
        ]);
    }

    public function removeBrokerageNote($index): void
    {
        unset($this->brokerageNotes[$index]);
    }

    public function extract(): void
    {
        $this->validate();

        /** @var BrokerageService $brokerageService */
        $brokerageService = app()->make(BrokerageService::class);

        /** @var User $user */
        $user = auth()->user();

        foreach ($this->brokerageNotes as $brokerageNote) {
            $brokerageService->extract($brokerageNote, Broker::find($brokerageNote['broker_id']), $user);
        }
    }
}
