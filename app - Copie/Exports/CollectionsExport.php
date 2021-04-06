<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class CollectionsExport implements FromCollection
{
    public function __construct($collections)
    {
        $this->collections = $collections;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->collections;
    }
}
