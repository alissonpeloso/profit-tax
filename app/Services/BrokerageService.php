<?php

namespace App\Services;

use App\Models\User;
use App\Models\Broker;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class BrokerageService
{
    public function extract(UploadedFile $file, Broker $broker, User $user, ?string $password = null): void
    {
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
        $path = $file->storeAs('uploads', Str::random(40));

        // Extract the data from the file running the python script
        $output = shell_exec("python3 brokerage_extractor/main.py $broker->identifier $path" . ($password ? " $password" : ''));

        // Delete the file
        unlink($path);

        try {
            return json_decode($output, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException('Invalid data extracted from the file');
        }
    }
}
