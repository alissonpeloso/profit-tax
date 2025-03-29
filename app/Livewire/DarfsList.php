<?php

namespace App\Livewire;

use App\Models\Darf;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Laravel\Jetstream\InteractsWithBanner;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DarfsList extends Component
{
    use InteractsWithBanner, WithPagination;

    public const array PAGE_SIZES = [10, 25, 50, 100];

    public string $search = '';
    public int $perPage = self::PAGE_SIZES[0];
    public string $sortBy = 'date';
    public string $sortDirection = 'desc';

    public function render()
    {
        return view('livewire.darfs-list', [
            'darfs' => $this->findDarfs(),
        ]);
    }

    public function findDarfs(): LengthAwarePaginator
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->darfs()
            ->search($this->search)
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function updatedSearch(): void
    {
        $this->invalidateCache();
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->invalidateCache();
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->invalidateCache();
        $this->resetPage();
    }

    public function updatedSortDirection(): void
    {
        $this->invalidateCache();
        $this->resetPage();
    }

    public function editStatus(int $darfId, string $status): void
    {
        $darf = Darf::findOrFail($darfId);
        $darf->update(['status' => $status]);
        $this->banner(__('Status updated successfully!'));
    }

    #[On('refresh-darfs-list')]
    public function invalidateCache(): void
    {
        $this->render();
    }
}
