<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait LogsActivity
{
    public function logActivity($message) {
        Log::info($message);
    }
}
