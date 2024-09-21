<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

class UploadBrokerageNote extends Component
{
    use WithFileUploads;

    #[Validate]
    public array $brokerageNotes = [];

    protected array $rules = [
        'brokerageNotes.*' => 'required|file|mimes:pdf',
    ];

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        return view('livewire.upload-brokerage-note');
    }

    public function removeBrokerageNote($index): void
    {
        unset($this->brokerageNotes[$index]);
    }
}
