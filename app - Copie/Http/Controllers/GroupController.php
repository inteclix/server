<?php

namespace App\Http\Controllers;

use App\Car;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;
use Carbon\Carbon;

use App\CarClient;
use App\CarClientDriver;
use App\Checklist;
use App\Client;
use App\Decharge;
use App\Driver;
use App\Group;
use App\User;
use App\Notification;
use App\Restitition;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\TryCatch;


class GroupController extends Controller
{
    function search(Request $request)
    {
        $data = $request->get('data');
        $groups = Group::where('name', 'like', "%{$data}%")
            ->take(8)
            ->get()->all();
        // dump($groups);
        $groups = $groups ? $groups : [];
        return $this->success($groups, "Success");
    }

    function getCars(Request $request, $id)
    {
        try {
            $group = Group::findOrFail($id);
        } catch (QueryException $e) {
            return $this->error("No group with this id");
        }
        $cars = $group->cars()->get();
        return $this->success($cars);
    }

    function setCars(Request $request, $id)
    {
        try {
            $group = Group::findOrFail($id);
        } catch (QueryException $e) {
            return $this->error("No group with this id");
        }
        $group->cars()->sync($request->cars_id);
        $this->success([]);
    }
}
