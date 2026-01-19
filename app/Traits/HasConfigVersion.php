<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasConfigVersion
{
    /**
     * Update the configuration version to trigger a reload on connected clients.
     */
    protected function updateConfigVersion()
    {
        Storage::disk('local')->put('config_version.txt', now()->timestamp);
    }
}
