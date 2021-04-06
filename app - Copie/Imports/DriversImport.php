<?php

namespace App\Imports;

use App\Driver;
use Maatwebsite\Excel\Concerns\ToModel;

class DriversImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Driver([
            "firstname" => $row[1],
            "lastname" => $row[2],
            "tel" => $row[3],
            "code_paie" => $row[4],
            "type" => $row[5],
            "createdby_id" => $row[6]
        ]);
    }
}
