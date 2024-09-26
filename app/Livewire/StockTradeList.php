<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\LengthAwarePaginator;

class StockTradeList extends Component
{
    public const array PAGE_SIZES = [10, 25, 50, 100];

    public string $search = '';
    public int $perPage = 10;
    protected ?LengthAwarePaginator $stockTrades = null;

    #[On('refresh-stock-trade-list')]
    public function render(): View|Factory|Application
    {
        return view('livewire.stock-trade-list');
    }

    public function stockTrades(): LengthAwarePaginator
    {
        if ($this->stockTrades) {
            return $this->stockTrades;
        }

        /** @var User $user */
        $user = auth()->user();

        $this->stockTrades = $user->stockTrades()
            ->search($this->search)
            ->orderBy('note_id', 'desc')
            ->orderBy('date', 'desc')
            ->paginate($this->perPage);

        return $this->stockTrades;
    }

    public function updatedSearch(): void
    {
        $this->invalidateCache();
    }

    public function updatedPerPage(): void
    {
        $this->invalidateCache();
    }

    protected function invalidateCache(): void
    {
        $this->stockTrades = null;
    }
}
