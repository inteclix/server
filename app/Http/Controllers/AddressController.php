<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    function search(Request $request)
    {
        $cities = DB::table("address")
            ->select([
                "id",
                "code_sap",
                "designation",
            ])
            ->where("code_sap", "like", "%{$request->data}%")
            ->orWhere("designation", "like", "%{$request->data}%")
            ->take(8)
            ->get()->all();

        return $this->http_ok($cities);
    }
}
