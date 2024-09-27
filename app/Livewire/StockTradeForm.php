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
    public ?StockTrade $stockTrade = null;

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

    #[Validate(rule: ['nullable', 'numeric', 'min:0.00'], as: 'fee')]
    public float $fee;

    #[Validate(rule: ['nullable', 'numeric', 'min:0.00'], as: 'ir')]
    public float $ir;

    #[Validate(rule: ['required', 'integer'], as: 'note identifier')]
    public int $noteId;

    #[Validate(rule: ['required', 'in:buy,sell'], as: 'operation')]
    public string $operation;

    public function mount(int|StockTrade|null $stockTrade): void
    {
        if (!$stockTrade) {
            return;
        }

        if (is_int($stockTrade)) {
            $stockTrade = StockTrade::findOrFail($stockTrade);
        }

        $this->stockTrade = $stockTrade;
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
        $title = $this->stockTrade ? __("Editing stock trade with ID {$this->stockTrade->id}") : __('New stock trade');

        return view('livewire.stock-trade-form', [
            'title' => $title,
        ]);
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'broker_id' => $this->brokerId,
            'date' => $this->date,
            'stock_symbol' => $this->stockSymbol,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'fee' => $this->fee,
            'ir' => $this->ir,
            'note_id' => $this->noteId,
            'operation' => $this->operation,
        ];

        if ($this->stockTrade) {
            $this->stockTrade->update($data);
            $this->dispatch('saved');

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
