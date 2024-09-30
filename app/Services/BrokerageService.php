<?php

namespace App\Services;

use JsonException;
use App\Models\User;
use App\Models\Broker;
use App\Models\StockTrade;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\BrokerageServiceException;
use Illuminate\Validation\ValidationException;

class BrokerageService
{
    /**
     * @throws BrokerageServiceException
     * @throws ValidationException
     */
    public function extract(UploadedFile $file, Broker|int $broker, User $user, ?string $password = null): void
    {
        // Get the broker instance
        if (!$broker instanceof Broker) {
            $broker = Broker::findOrFail($broker);
        }

        $data = $this->extractFromFile($file, $broker, $password);

        // Validate the data
        $data = Validator::make($data, [
            '*.date' => 'required|date',
            '*.stock_symbol' => 'required|string',
            '*.quantity' => 'required|numeric',
            '*.price' => 'required|numeric',
            '*.fee' => 'nullable|numeric',
            '*.ir' => 'nullable|numeric',
            '*.note_id' => 'required|string',
            '*.operation' => ['required', Rule::in(array_keys(StockTrade::OPERATIONS))],
        ], [], [
            '*.date' => 'date',
            '*.stock_symbol' => 'stock symbol',
            '*.quantity' => 'quantity',
            '*.price' => 'price',
            '*.fee' => 'fee',
            '*.ir' => 'IR',
            '*.note_id' => 'note ID',
            '*.operation' => 'operation',
        ])->validate();

        // Checking if this brokerage note has already been uploaded
        $noteId = Arr::get($data, '0.note_id');
        $alreadyUploaded = $broker->stockTrades()->where('note_id', $noteId)->exists();

        if ($alreadyUploaded) {
            throw new BrokerageServiceException('The brokerage note has already been uploaded.');
        }

        // Add user_id and broker_id to the data
        $data = array_map(function ($trade) use ($user, $broker, $data) {
            if (!isset($trade['ir']) || !$trade['ir']) {
                $trade['ir'] = 0;
            }

            if (!isset($trade['fee']) || !$trade['fee']) {
                $trade['fee'] = 0;
            }

            return array_merge($trade, [
                'class' => $this->getStockClass($trade['stock_symbol']),
                'is_exempt' => $this->isExempt($trade['stock_symbol']),
                'is_day_trade' => $this->isIntraDay($trade, $data),
                'user_id' => $user->id,
                'broker_id' => $broker->id,
            ]);
        }, $data);

        // Save the data in the database
        $broker->stockTrades()->insert($data);
    }

    /**
     * Extract the data from the brokerage note file.
     *
     * @throws BrokerageServiceException
     */
    protected function extractFromFile(UploadedFile $file, Broker $broker, ?string $password): array
    {
        $fileName = $file->getClientOriginalName();
        $path = $file->getRealPath();

        // Extract the data from the file running the python script
        $pythonScriptPath = base_path('brokerage_extractor/main.py');
        $command = "python3 $pythonScriptPath $broker->identifier $path" . ($password ? " $password" : '');
        $output = shell_exec($command);

        try {
            $decoded = json_decode($output, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new BrokerageServiceException('The brokerage note could not be extracted from the file: ' . $fileName);
        }

        if (!Arr::get($decoded, 'error')) {
            return $decoded;
        }

        $errorMessage = $decoded['error']['message'] ?? 'The brokerage note could not be extracted.';
        $exceptionMessage = $decoded['error']['exception'] ?? '';

        throw new BrokerageServiceException($errorMessage . ' ' . $exceptionMessage . ' File: ' . $fileName);
    }

    protected function isIntraDay(array $trade, array $data): bool
    {
        if ($trade['operation'] === StockTrade::OPERATION_BUY) {
            return false;
        }

        $date = $trade['date'];
        $stockSymbol = $trade['stock_symbol'];

        return Arr::where($data, function ($trade) use ($date, $stockSymbol) {
            return $trade['date'] === $date && $trade['stock_symbol'] === $stockSymbol && $trade['operation'] === StockTrade::OPERATION_BUY;
        }) !== [];
    }

    protected function isExempt(string $stockSymbol): bool
    {
        $fiiStocks = $this->retrieveExemptList();

        return in_array($stockSymbol, $fiiStocks);
    }

    protected function getStockClass(string $stockSymbol): string
    {
        $stocks = array_merge($this->retrieveStockList(), $this->retrieveExemptList());
        $bdrStocks = $this->retrieveBDRList();
        $etfStocks = $this->retrieveETFList();
        $fiiStocks = $this->retrieveFIIList();

        if (in_array($stockSymbol, $stocks)) {
            return StockTrade::CLASS_STOCK;
        }

        if (in_array($stockSymbol, $bdrStocks)) {
            return StockTrade::CLASS_BDR;
        }

        if (in_array($stockSymbol, $etfStocks)) {
            return StockTrade::CLASS_ETF;
        }

        if (in_array($stockSymbol, $fiiStocks)) {
            return StockTrade::CLASS_FII;
        }

        return StockTrade::CLASS_STOCK;
    }

    protected function retrieveStockList(): array
    {
        $data = file_get_contents(base_path('resources/data/brazil_stocks.data'));

        return explode("\n", $data);
    }

    protected function retrieveExemptList(): array
    {
        $data = file_get_contents(base_path('resources/data/exempt_stocks.data'));

        return explode("\n", $data);
    }

    protected function retrieveBDRList(): array
    {
        $data = file_get_contents(base_path('resources/data/BDR.data'));

        return explode("\n", $data);
    }

    protected function retrieveETFList(): array
    {
        $data = file_get_contents(base_path('resources/data/ETF.data'));

        return explode("\n", $data);
    }

    protected function retrieveFIIList(): array
    {
        $data = file_get_contents(base_path('resources/data/FII.data'));

        return explode("\n", $data);
    }
}
