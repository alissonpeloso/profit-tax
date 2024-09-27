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
        $data = array_map(function ($trade) use ($user, $broker) {
            if (!isset($trade['ir']) || !$trade['ir']) {
                $trade['ir'] = 0;
            }

            if (!isset($trade['fee']) || !$trade['fee']) {
                $trade['fee'] = 0;
            }

            return array_merge($trade, [
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
}
