<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    function search(Request $request)
    {
        $cities = DB::table("cities")
            ->select([
                "id",
                "wilaya_name",
                "daira_name",
                "commune_name",
            ])
            ->where("wilaya_name", "like", "%{$request->data}%")
            ->orWhere("daira_name", "like", "%{$request->data}%")
            ->orWhere("commune_name", "like", "%{$request->data}%")
            ->take(8)
            ->get()->all();

        return $this->http_ok($cities);
    }
}
