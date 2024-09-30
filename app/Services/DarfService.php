<?php

namespace App\Services;

use App\Models\User;

class DarfService
{
    public function calculateDarfValues(): array
    {
        /** @var User $user */
        $user = auth()->user();

    }
}
