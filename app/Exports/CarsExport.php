<?php

namespace App\Exports;

use App\Car;
use Maatwebsite\Excel\Concerns\FromCollection;

class CarsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Car::all();
    }
}
