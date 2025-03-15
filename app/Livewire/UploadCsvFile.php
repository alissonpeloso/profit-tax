<?php

namespace App\Livewire;

use Laravel\Jetstream\InteractsWithBanner;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadCsvFile extends Component
{
    use InteractsWithBanner, WithFileUploads;

    public $file;

    public function render()
    {
        return view('livewire.upload-csv-file');
    }
}
