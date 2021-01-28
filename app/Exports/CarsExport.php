<?php

namespace App\Exports;

use App\Car;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;

class CarsExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $items = DB::table("cars")
            ->leftJoin('car_states', function ($join) {
                $join->on('cars.id', '=', 'car_states.car_id')
                    ->on('car_states.id', '=', DB::raw("(select max(id) from car_states WHERE car_states.car_id = cars.id)"));
            })
            ->leftJoin('checklists', function ($join) {
                $join->on('cars.id', '=', 'checklists.car_id')
                    ->on('checklists.id', '=', DB::raw("(select max(id) from checklists WHERE checklists.car_id = cars.id)"));
            })
            ->leftJoin("restititions", "checklists.id", "=", "restititions.checklist_id")
            ->leftJoin("decharges", "decharges.id", "=", "checklists.decharge_id")
            ->leftJoin("drivers", "drivers.id", "=", "checklists.driver_id")
            ->leftJoin("clients", "clients.id", "=", "decharges.client_id")
            ->leftJoin("car_group", "car_group.car_id", "=", "cars.id")
            ->leftJoin("groups", "car_group.group_id", "=", "groups.id")
            ->select([
                "cars.code_gps",
                "cars.matricule",
                "cars.genre",
                "cars.marque",
                "groups.name as groupName",
                "clients.designation as client",
                DB::raw("CONCAT(drivers.firstname, ' ',drivers.lastname) as drivers_fullname"),
                "car_states.name as state",
            ])->get();
        $items->prepend([
            "code_gps" => "CODE GPS",
            "matricule" => "MATRICULE",
            "genre" => "GENRE",
            "marque" => "MARQUE",
            "groupName" => "GROUPE",
            "client" => "CLIENT",
            "drivers_fullname" => "CONDUCTEUR",
            "state" => "STATUS"
        ]);
        return $items;
    }
}
