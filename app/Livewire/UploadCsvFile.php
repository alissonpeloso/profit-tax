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
use NumberFormatter;

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
        $nodeId = 'csv-' . time() . '-' . uniqid();
        foreach ($data as $row) {
            $price = NumberFormatter::create('pt_BR', NumberFormatter::DECIMAL)
                ->parse($row[$this->priceHeader]);
            $quantity = NumberFormatter::create('pt_BR', NumberFormatter::INTEGER_DIGITS)
                ->parse($row[$this->quantityHeader]);

            $operation = StockTradeOperation::EXTRAORDINARY;
            if ($quantity < 0 || $price < 0) {
                $operation = StockTradeOperation::SELL;
            } elseif ($quantity > 0) {
                $operation = StockTradeOperation::BUY;
            }

            // Set data indexes to insert into the database
            $NewData[] = [
                'date' => Carbon::createFromFormat('d/m/Y', $row[$this->dateHeader]),
                'stock_symbol' => $row[$this->stockSymbolHeader],
                'quantity' => $quantity,
                'price' => $price,
                'fee' => NumberFormatter::create('en_US', NumberFormatter::DECIMAL)
                    ->parse($row[$this->feeHeader]),
                'ir' => NumberFormatter::create('en_US', NumberFormatter::DECIMAL)
                    ->parse($row[$this->irHeader]),
                'note_id' => $this->noteIdHeader ? $row[$this->noteIdHeader] : $nodeId,
                'operation' => $operation,
                'broker' => $row[$this->brokerHeader],
            ];
        }

        return $NewData;
    }
}
