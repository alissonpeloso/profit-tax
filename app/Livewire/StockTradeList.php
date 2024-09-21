<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\StockTrade;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;

class StockTradeList extends Component
{
    public string $search = '';
    public int $perPage = 10;

    public function render(): View|Factory|Application
    {
        return view('livewire.stock-trade-list', [
            'stockTrades' => $this->getStockTradesGroupedByDate(),
        ]);
    }

    protected function getStockTradesGroupedByDate()
    {
        /** @var User $user */
        $user = auth()->user();

        return StockTrade::where('user_id', $user->id)
            ->search($this->search)
            ->orderBy('date', 'desc')
            ->paginate($this->perPage);
    }
}
