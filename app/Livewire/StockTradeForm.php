<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\StockTrade;
use Livewire\Attributes\Validate;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;

/**
 * Class StockTradeForm.
 *
 * Dispatches:
 * - saved
 * - cancel
 */
class StockTradeForm extends Component
{
    protected ?StockTrade $stockTrade = null;

    #[Validate(rule: ['required', 'integer', 'exists:brokers,id'], as: 'broker')]
    public int $brokerId;

    #[Validate(rule: ['required', 'date_format:Y-m-d'], as: 'date')]
    public string $date;

    #[Validate(rule: ['required', 'string', 'max:10'], as: 'stockSymbol')]
    public string $stockSymbol;

    #[Validate(rule: ['required', 'integer', 'min:1'], as: 'quantity')]
    public int $quantity;

    #[Validate(rule: ['required', 'numeric', 'min:0.01'], as: 'price')]
    public float $price;

    #[Validate(rule: ['nullable', 'numeric', 'min:0.01'], as: 'fee')]
    public float $fee;

    #[Validate(rule: ['nullable', 'numeric', 'min:0.01'], as: 'ir')]
    public float $ir;

    #[Validate(rule: ['required', 'integer'], as: 'note identifier')]
    public int $noteId;

    #[Validate(rule: ['required', 'in:buy,sell'], as: 'operation')]
    public string $operation;

    public function mount(?int $stockTradeId = null): void
    {
        if (!$stockTradeId) {
            return;
        }

        $this->stockTrade = StockTrade::findOrFail($stockTradeId);
        $this->brokerId = $this->stockTrade->broker_id;
        $this->date = $this->stockTrade->date->format('Y-m-d');
        $this->stockSymbol = $this->stockTrade->stock_symbol;
        $this->quantity = $this->stockTrade->quantity;
        $this->price = $this->stockTrade->price;
        $this->fee = $this->stockTrade->fee;
        $this->ir = $this->stockTrade->ir;
        $this->noteId = $this->stockTrade->note_id;
        $this->operation = $this->stockTrade->operation;
    }

    public function render(): View|Factory|Application
    {
        return view('livewire.stock-trade-form');
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->stockTrade) {
            $this->stockTrade->update($data);

            return;
        }

        StockTrade::create($data);
        $this->dispatch('saved');
    }

    public function cancel(): void
    {
        $this->dispatch('cancel');
    }
}
