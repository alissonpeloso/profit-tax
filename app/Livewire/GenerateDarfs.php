<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\DarfService;
use Illuminate\Support\Number;
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

        $this->darfs = $darfService->calculateDarfValues(true);
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

            if (Number::format($savedDarf->value, 2) === Number::format($darf['value'], 2)) {
                unset($this->darfs[$key]);

                continue;
            }

            // Add an error message to this darf
            $this->addError('darfs.' . $key, 'This DARF has already been saved with a different value.');
        }
    }

    public function saveDarf(string $darfKey, bool $update = false): void
    {
        $darfData = $this->darfs[$darfKey];
        /** @var User $user */
        $user = auth()->user();

        if ($update) {
            $darf = $user->darfs()->where('date', $darfData['date'])->first();
            $darf->update($darfData);
            unset($this->darfs[$darfKey]);

            return;
        }

        $user->darfs()->create($darfData);
        unset($this->darfs[$darfKey]);
    }

    public function ignoreDarf(string $darfKey): void
    {
        unset($this->darfs[$darfKey]);
    }

    public function saveAll(): void
    {
        foreach ($this->darfs as $key => $darf) {
            if ($this->getErrorBag()->has('darfs.' . $key)) {
                continue;
            }

            $this->saveDarf($key);
        }
    }

    public function resetAll(): void
    {
        $this->darfs = [];
    }
}
