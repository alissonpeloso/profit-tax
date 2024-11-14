<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\DarfService;
use Illuminate\View\Factory;
use Illuminate\View\View;
use Livewire\Component;

class GenerateDarfs extends Component
{
    public array $darfs = [];

    public function render(): Factory|View
    {
        return view('livewire.generate-darfs');
    }

    public function generateDarfs(): void
    {
        /** @var DarfService $darfService */
        $darfService = app()->make(DarfService::class);

        $this->darfs = $darfService->calculateDarfValues();
        $this->compareWithExistingDarfs();
    }

    protected function compareWithExistingDarfs(): void
    {
        /** @var User $user */
        $user = auth()->user();
        $userDarfs = $user->darfs()->get();

        foreach ($this->darfs as $key => $darf) {
            $darfDate = $darf['date'];
            $savedDarf = $userDarfs->where('date', $darfDate)->first();

            if (!$savedDarf) {
                continue;
            }

            if ($savedDarf->value = $darf->value) {
                unset($this->darfs[$key]);
            }

            // Add an error message to this darf
            $this->addError('darfs.' . $key, 'This DARF has already been saved with a different value.');
        }
    }

    public function saveDarf(string $darfKey): void
    {
        $darf = $this->darfs[$darfKey];

        $darf->save();
    }
}
