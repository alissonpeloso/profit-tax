<?php

namespace App\Livewire;

use App\Enum\StockTradeOperation;
use App\Models\Broker;
use App\Models\User;
use App\Services\BrokerageService;
use Illuminate\Support\Carbon;
use Laravel\Jetstream\InteractsWithBanner;
use League\Csv\Reader;
use Livewire\Component;
use Livewire\WithFileUploads;
use Str;

class UploadCsvFile extends Component
{
    use InteractsWithBanner, WithFileUploads;

    public $file;
    public string $dateHeader;
    public string $stockSymbolHeader;
    public string $quantityHeader;
    public string $priceHeader;
    public string $feeHeader;
    public string $irHeader;
    public ?string $noteIdHeader = null;
    public string $brokerHeader;
    public array $csvHeaders = [];
    public array $columns = [];

    public function mount()
    {
        $this->columns = $this->findNecessaryColumns();
    }

    public function render()
    {
        return view('livewire.upload-csv-file', [
            'columns' => $this->columns,
        ]);
    }

    public function updatedFile()
    {
        $csv = Reader::createFromPath($this->file->getRealPath(), 'r');
        $csv->setHeaderOffset(0);

        $this->csvHeaders = $csv->getHeader();
    }

    protected function findNecessaryColumns()
    {
        return [
            'dateHeader' => __('Date'),
            'stockSymbolHeader' => __('Stock Symbol'),
            'quantityHeader' => __('Quantity'),
            'priceHeader' => __('Price'),
            'feeHeader' => __('Fee'),
            'irHeader' => __('IR'),
            'noteIdHeader' => __('Note ID'),
            'brokerHeader' => __('Broker'),
        ];
    }

    protected function rules()
    {
        return [
            'file' => 'required|file|mimes:csv,txt',
            'dateHeader' => 'required|string',
            'stockSymbolHeader' => 'required|string',
            'quantityHeader' => 'required|string',
            'priceHeader' => 'required|string',
            'feeHeader' => 'required|string',
            'irHeader' => 'required|string',
            'noteIdHeader' => 'nullable|string',
            'brokerHeader' => 'required|string',
        ];
    }

    public function extract()
    {
        $this->validate();

        try {
            $data = $this->extractDataFromCsv();
        } catch (\Exception $e) {
            $this->dangerBanner('Error reading the CSV file: ' . $e->getMessage());

            return;
        }

        $errors = [];
        // For each different broker, insert the data into the database
        collect($data)->groupBy('broker')->each(function ($group) use (&$errors) {
            $brokerName = $group->first()['broker'];

            // Here you would find the broker by name
            $broker = Broker::where('name', 'ilike', "%{$brokerName}%")->first();

            if (!$broker) {
                $errors[] = "Broker {$brokerName} not found.";

                return;
            }

            /** @var BrokerageService $brokerageService */
            $brokerageService = app(BrokerageService::class);

            /** @var User $user */
            $user = auth()->user();

            try {
                $brokerageService->extract($broker, $user, $group->toArray());
            } catch (\Exception $e) {
                $errors[] = "Error processing broker {$brokerName}: " . $e->getMessage();
            }
        });

        if (!empty($errors)) {
            $this->dangerBanner('There were errors during the upload: ' . implode(', ', $errors));

            return;
        }

        $this->banner('The CSV file has been successfully uploaded.');

        // Reset the default values and close the parent modal
        $this->reset();
        $this->dispatch('close-modal');
        $this->dispatch('refresh-stock-trade-list')->to(StockTradeList::class);
    }

    protected function extractDataFromCsv()
    {
        $csv = Reader::createFromPath($this->file->getRealPath(), 'r');
        $csv->setHeaderOffset(0);

        $data = $csv->getRecords();

        $NewData = [];
        $noteId = 'csv-' . time() . '-' . uniqid();
        foreach ($data as $row) {
            $date = Carbon::createFromFormat('d/m/Y', $row[$this->dateHeader]);
            $stockSymbol = Str::upper($row[$this->stockSymbolHeader]);
            $price = (float) Str::replace(',', '.', $row[$this->priceHeader]);
            $quantity = (int) Str::replace(',', '', $row[$this->quantityHeader]);
            $fee = (float) Str::replace(',', '.', $row[$this->feeHeader]);
            $ir = (float) Str::replace(',', '.', $row[$this->irHeader]);
            $broker = $row[$this->brokerHeader];

            $operation = StockTradeOperation::EXTRAORDINARY->value;
            if ($quantity < 0 || $price < 0) {
                $operation = StockTradeOperation::SELL->value;
            } elseif ($quantity > 0) {
                $operation = StockTradeOperation::BUY->value;
            }

            $quantity = abs($quantity);
            $price = abs($price);

            // Set data indexes to insert into the database
            $NewData[] = [
                'date' => $date,
                'stock_symbol' => $stockSymbol,
                'quantity' => $quantity,
                'price' => $price,
                'fee' => $fee,
                'ir' => $ir,
                'note_id' => $this->noteIdHeader ? $row[$this->noteIdHeader] : $noteId,
                'operation' => $operation,
                'broker' => $broker,
            ];
        }

        return $NewData;
    }
}
