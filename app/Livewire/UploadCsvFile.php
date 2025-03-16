<?php

namespace App\Livewire;

use Laravel\Jetstream\InteractsWithBanner;
use League\Csv\Reader;
use Livewire\Component;
use Livewire\WithFileUploads;

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
    public string $operationHeader;
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
            'operationHeader' => __('Operation'),
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
            'operationHeader' => 'required|string',
        ];
    }

    public function extract()
    {
        $this->validate();

        $csv = Reader::createFromPath($this->file->getRealPath(), 'r');
        $csv->setHeaderOffset(0);

        $data = $csv->getRecords();

        foreach ($data as $row) {
            // Set data indexes to insert into the database
            $NewData[] = [
                'date' => $row[$this->dateHeader],
                'stock_symbol' => $row[$this->stockSymbolHeader],
                'quantity' => $row[$this->quantityHeader],
                'price' => $row[$this->priceHeader],
                'fee' => $row[$this->feeHeader],
                'ir' => $row[$this->irHeader],
                'note_id' => $this->noteIdHeader ? $row[$this->noteIdHeader] : null,
                'operation' => $row[$this->operationHeader],
            ];
        }

        $this->banner('The CSV file has been successfully uploaded.', 'success');
    }
}
