<?php

namespace App\Exports;

use App\Farmer;
use Maatwebsite\Excel\Concerns\FromCollection;

class FarmersExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Farmer::all();
    }
}
