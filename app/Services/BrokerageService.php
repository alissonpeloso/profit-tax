<?php

namespace App\Services;

use App\Models\User;
use App\Models\Broker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class BrokerageService
{
    public function extract(UploadedFile $file, Broker|int $broker, User $user, ?string $password = null): void
    {
        // Get the broker instance
        if (!$broker instanceof Broker) {
            $broker = Broker::findOrFail($broker);
        }

        $data = $this->extractFromFile($file, $broker, $password);

        // Validate the data
        Validator::make($data, [
            '*.date' => 'required|date',
            '*.stock_symbol' => 'required|string',
            '*.quantity' => 'required|numeric',
            '*.price' => 'required|numeric',
            '*.fee' => 'required|numeric',
            '*.ir' => 'required|numeric',
            '*.note_id' => 'required|string',
            '*.operation' => 'required|in:buy,sell',
        ])->validate();

        // Add user_id and broker_id to the data
        $data = array_map(function ($trade) use ($user, $broker) {
            return array_merge($trade, [
                'user_id' => $user->id,
                'broker_id' => $broker->id,
            ]);
        }, $data);

        // Save the data in the database
        $broker->stockTrades()->insert($data);
    }

    protected function extractFromFile(UploadedFile $file, Broker $broker, ?string $password): array
    {
        // Save the file in a temporary location
        $path = $file->getRealPath();

        // Extract the data from the file running the python script
        $pythonScriptPath = base_path('brokerage_extractor/main.py');
        $command = "python3 $pythonScriptPath $broker->identifier $path" . ($password ? " $password" : '');
        $output = shell_exec($command);

        // Delete the file
        unlink($path);

        try {
            return json_decode($output, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException('Invalid data extracted from the file');
        }
    }
}
