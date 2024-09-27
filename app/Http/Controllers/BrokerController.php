<?php

namespace App\Http\Controllers;

use App\Models\Broker;
use Illuminate\Support\Arr;
use Illuminate\Http\JsonResponse;

class BrokerController extends Controller
{
    public function search(): JsonResponse
    {
        $data = request()->validate([
            'search' => 'nullable|string',
            'selected' => 'nullable|array',
        ]);

        $brokers = Broker::query()
            ->select('id', 'name')
            ->when(
                Arr::get($data, 'search'),
                fn ($query, $search) => $query->search($search)
            )
            ->when(
                Arr::get($data, 'selected'),
                fn ($query, $selected) => $query->whereIn('id', $selected)
            )
            ->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json($brokers);
    }
}
