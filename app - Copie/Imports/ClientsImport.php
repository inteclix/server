<?php

namespace App\Imports;

use App\Client;
use Maatwebsite\Excel\Concerns\ToModel;

class ClientsImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
       // dump($row);
        return new Client([
            "code" => $row[1],
            "designation" => $row[2],
            "localite" => $row[3],
            "client_id" => $row[4],
            "createdby_id" => $row[5]
        ]);
    }
}
