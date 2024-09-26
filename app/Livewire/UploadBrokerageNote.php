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
use Laravel\Jetstream\InteractsWithBanner;

class UploadBrokerageNote extends Component
{
    use InteractsWithBanner, WithFileUploads;

    #[Validate([
        'brokerageNotes' => 'required|array',
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

    #[Validate([
        'brokerageNotePasswords.*' => 'required|string',
    ], [
        'brokerageNotePasswords.*.required' => 'The brokerage note password is required.',
        'brokerageNotePasswords.*.string' => 'The brokerage note password must be a string.',
    ])]
    public array $brokerageNotePasswords = [];

    public bool $shouldShowPasswords = false;
    public string $defaultPassword = '';

    #[Validate([
        'defaultBroker' => 'nullable|exists:brokers,id',
    ], [
        'defaultBroker.exists' => 'The default broker is invalid.',
    ])]
    public string $defaultBroker = '';

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
        $this->unsetBrokerageNote($index);
    }

    public function extract(): void
    {
        $this->validate();
        $firstBrokerIdAvailable = $this->getBrokersProperty()->first()->id;

        /** @var BrokerageService $brokerageService */
        $brokerageService = app()->make(BrokerageService::class);

        /** @var User $user */
        $user = auth()->user();

        $successfulExports = [];
        $hasErrors = false;
        foreach ($this->brokerageNotes as $index => $brokerageNote) {
            $brokerId = $this->selectedBrokers[$index] ?? $firstBrokerIdAvailable;
            $brokerageNotePassword = $this->brokerageNotePasswords[$index] ?? null;

            try {
                $brokerageService->extract($brokerageNote, $brokerId, $user, $brokerageNotePassword);
                $this->unsetBrokerageNote($index);
                $successfulExports[] = $brokerageNote->getClientOriginalName();
            } catch (\Throwable $e) {
                $this->addError('brokerageNotes.' . $index, $e->getMessage());
                $hasErrors = true;
            }
        }

        if (count($successfulExports) > 0) {
            $message = 'Brokerage note ' . implode(', ', $successfulExports) . ' was successfully extracted.';
            $this->banner($message);
        }

        if ($hasErrors) {
            return;
        }

        // Reset the default values and close the parent modal
        $this->reset();
        $this->dispatch('close-modal');
        $this->dispatch('refresh-stock-trade-list')->to(StockTradeList::class);
    }

    protected function unsetBrokerageNote(int $index): void
    {
        unset($this->brokerageNotes[$index]);
        unset($this->selectedBrokers[$index]);
        unset($this->brokerageNotePasswords[$index]);
    }

    public function togglePasswordVisibility(): void
    {
        $this->shouldShowPasswords = !$this->shouldShowPasswords;
    }

    public function updatedDefaultPassword(): void
    {
        $this->brokerageNotePasswords = array_fill(0, count($this->brokerageNotes), $this->defaultPassword);
    }

    public function updatedDefaultBroker(): void
    {
        $this->selectedBrokers = array_fill(0, count($this->brokerageNotes), $this->defaultBroker);
    }
}
