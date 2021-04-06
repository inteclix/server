<?php

namespace App\Imports;

use App\Car;
use Maatwebsite\Excel\Concerns\ToModel;

class CarsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        dump($row);
        return new Car([
            'matricule' => $row[1],
            'prop' => $row[2],
            'old_matricule' => $row[3],
            'code_gps' => $row[4],
            'genre' => $row[5],
            'marque' => $row[6],
            'type' => $row[7],
            'puissance' => $row[8],
            'energie' => $row[9],
            'carrosserie' => $row[10],
            'color' => $row[11],
            'createdby_id' => $row[12],
        ]);
    }
}
