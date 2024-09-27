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
    public ?int $editingStockTradeId = null;
    protected ?LengthAwarePaginator $stockTradesCache = null;

    #[On('refresh-stock-trade-list')]
    public function render(): View|Factory|Application
    {
        return view('livewire.stock-trade-list');
    }

    public function stockTrades(): LengthAwarePaginator
    {
        if ($this->stockTradesCache) {
            return $this->stockTradesCache;
        }

        /** @var User $user */
        $user = auth()->user();

        $this->stockTradesCache = $user->stockTrades()
            ->search($this->search)
            ->orderBy('date', 'desc')
            ->orderBy('note_id', 'desc')
            ->orderBy('operation', 'asc')
            ->orderBy('stock_symbol', 'asc')
            ->paginate($this->perPage);

        return $this->stockTradesCache;
    }

    public function updatedSearch(): void
    {
        $this->invalidateCache();
    }

    public function updatedPerPage(): void
    {
        $this->invalidateCache();
    }

    #[On('refresh-stock-trade-list')]
    public function invalidateCache(): void
    {
        $this->stockTradesCache = null;
    }
}
