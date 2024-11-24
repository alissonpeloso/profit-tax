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
    public bool $alreadyGenerated = false;

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

        $this->alreadyGenerated = true;
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

            if (abs($savedDarf->value == $darf['value']) < PHP_FLOAT_EPSILON) {
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

    public function resetAll(): void
    {
        $this->darfs = [];
        $this->alreadyGenerated = false;
    }
}
