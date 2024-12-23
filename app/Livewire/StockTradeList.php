<?php

namespace App\Livewire;

use App\Models\StockTrade;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Jetstream\InteractsWithBanner;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class StockTradeList extends Component
{
    use InteractsWithBanner, WithPagination;

    public const array PAGE_SIZES = [10, 25, 50, 100];

    public string $search = '';
    public int $perPage = self::PAGE_SIZES[0];
    public ?int $editingStockTradeId = null;
    protected ?LengthAwarePaginator $stockTradesCache = null;

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

    public function delete(int $stockTradeId): void
    {
        $deleted = StockTrade::where('id', $stockTradeId)->delete();

        if (!$deleted) {
            $this->dangerBanner(__('The stock trade could not be deleted.'));

            return;
        }

        $this->banner(__('The stock trade has been deleted.'));
        $this->invalidateCache();
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

    #[On('refresh-stock-trade-list')]
    public function invalidateCache(): void
    {
        $this->stockTradesCache = null;
        $this->render();
    }
}
