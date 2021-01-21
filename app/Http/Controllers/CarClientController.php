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
use App\Client;
use App\Driver;
use App\User;
use App\Notification;
use DateTime;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\TryCatch;

class CarClientController extends Controller
{
    function get($id, Request $request)
    {
        try {
            $c = CarClient::findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        $c->car = Car::find($c->car_id);
        $c->client = Client::find($c->client_id);
        $c->createdby = User::find($c->createdby_id);
        $c->driver = Driver::find($c->driver_id);
        return new JsonResponse([
            'message' => 'Success get',
            'data' => $c
        ]);
    }

    function getAffectations(Request $request)
    {
        $has_role = true;
        foreach ($request->auth->roles as $role) {
            if (
                $role->name === "ADD_EDIT_AFFECTATIONS_CLIENTS" ||
                $role->name === "ADD_EDIT_AFFECTATIONS_DRIVERS_CHECK_LIST" ||
                $role->name === "ADD_EDIT_AFFECTATIONS_DRIVERS"
            ) {
                $has_role = true;
            }
        }
        if ($has_role) {
            $date1 = $request->get('date1');
            $date2 = $request->get('date2');
            if ($date1 && $date2) {
                $resti = [$date1, null];
                $all = CarClient::orderBy('id', 'desc')
                    ->where('date_affectation', '<=', "{$date2}")
                    ->where(function ($q) use ($date1) {
                        $q->where('date_restitition', '>=', "{$date1}")
                            ->orWhere('date_restitition', '=', null);
                    })
                    ->get()->all();
                foreach ($all as $a) {
                    $a->car = Car::find($a->car_id);
                    $a->client = Client::find($a->client_id);
                }
                return new JsonResponse([
                    'message' => 'Success get all',
                    'data' => $all !== NULL ? $all : []
                ], Response::HTTP_OK);
            }
            $all = CarClient::orderBy('id', 'desc')->take(100)->get()->all();
            foreach ($all as $a) {
                $a->car = Car::find($a->car_id);
                $a->client = Client::find($a->client_id);
            }
            return new JsonResponse([
                'message' => 'Success get all',
                'data' => $all !== NULL ? $all : []
            ], Response::HTTP_OK);
        } else {
            return new JsonResponse([
                'message' => 'UNAUTHORIZED',
                'data' => []
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    function create(Request $request)
    {
        $has_role_gm = false;
        $has_role_ga = false;
        $has_role_com = false;
        foreach ($request->auth->roles as $role) {
            if ($role->name === "ADD_EDIT_AFFECTATIONS_CLIENTS") {
                $has_role_com = true;
            }
            if ($role->name === "ADD_EDIT_AFFECTATIONS_DRIVERS_CHECK_LIST") {
                $has_role_gm = true;
            }
            if ($role->name === "ADD_EDIT_AFFECTATIONS_DRIVERS") {
                $has_role_ga = true;
            }
        }
        if ($has_role_com) {
            $this->validate($request, [
                'client_id' => 'required',
                'car_id' => 'required',
                'date_affectation' => 'required',
            ]);
            $cc = CarClient::where('car_id', $request->car_id)->orderBy('created_at', 'desc')->first();
            if ($cc &&  !$cc->date_restitition) {
                return new JsonResponse([
                    'message' => "vehicule deja affecte"
                ], Response::HTTP_BAD_REQUEST);
            }

            try {
                $carClient = CarClient::create([
                    'client_id' => $request->client_id,
                    'car_id' => $request->car_id,
                    'affectation_client_comment' => $request->affectation_client_comment,
                    'date_affectation' => Carbon::parse($request->date_affectation),
                    'date_end_prestation' => $request->date_end_prestation ? Carbon::parse($request->date_end_prestation) : null,

                    'createdby_id' => $request->auth->id,
                ])->get()[0];
            } catch (QueryException $e) {
                return new JsonResponse([
                    'message' => $e
                ], Response::HTTP_BAD_REQUEST);
            }

            //$c->
            //   dump($carClient);
            $car = Car::find($request->car_id);
            $driver = $car->latestDriver();
            $client = Client::find($request->client_id);
            $users = User::all()->except($request->auth->id);
            foreach ($users as $user) {
                $n = new Notification;
                $n->title = "Nouvelle Affectation Vehicule: " . $car->matricule . ", pour Client: " . $client->designation;
                if ($driver) {
                    $n->sub_title = "Conducteur: " . $driver->firstname . " " . $driver->lastname;
                } else {
                    $n->sub_title = "Conducteur:  Sans Conducteur";
                }
                $n->url = "/affectations/show/" . $carClient->id;
                $n->from_id =  $request->auth->id;
                $n->to_id = $user->id;
                try {
                    $n->save();
                } catch (QueryException $e) {
                    $carClient->delete();
                    return new JsonResponse([
                        'message' => $e
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            return new JsonResponse([
                'message' => 'Success',
                'data' => $carClient
            ], Response::HTTP_CREATED);
        }
        return new JsonResponse([
            'message' => 'Permission denied'
        ], Response::HTTP_UNAUTHORIZED);
    }

    function delete($id, Request $request)
    {
        try {
            $c = CarClient::findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        $created_at = new DateTime($c->created_at);
        $diff = (int)$created_at->diff(new DateTime())->format('%h');
        if ($diff > 24) {
            return new JsonResponse([
                'message' => 'Permission denied'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $has_role = true;
        foreach ($request->auth->roles as $role) {
            if ($role->name === "ADD_EDIT_AFFECTATIONS_CLIENTS") {
                $has_role = true;
            }
        }
        if ($has_role) {
            CarClient::destroy($id);
            return new JsonResponse([
                'message' => 'Deleted'
            ]);
        }

        return new JsonResponse([
            'message' => 'Permission denied'
        ], Response::HTTP_UNAUTHORIZED);
    }

    function update($id, Request $request)
    {
        try {
            $c = CarClient::findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        $created_at = new DateTime($c->created_at);
        $diff = (int)$created_at->diff(new DateTime())->format('%h');
        dump($diff);
        if ($diff > 24) {
            return new JsonResponse([
                'message' => 'Permission denied'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $has_role = true;
        foreach ($request->auth->roles as $role) {
            if ($role->name === "ADD_EDIT_AFFECTATIONS_CLIENTS") {
                $has_role = true;
            }
        }
        if ($has_role) {
            $c->client_id = $request->client_id;
            $c->car_id = $request->car_id;
            $c->date_affectation = Carbon::parse($request->date_affectation);
            $c->date_end_prestation = $request->date_end_prestation ? Carbon::parse($request->date_end_prestation) : null;
            $c->affectation_client_comment = $request->affectation_client_comment;
            $c->updatedby_id = $request->auth->id;
            try {
                $c->save();
            } catch (QueryException $e) {
                return new JsonResponse([
                    'message' => $e
                ], Response::HTTP_BAD_REQUEST);
            }
            $car = Car::find($request->car_id);
            $driver = $car->latestDriver();
            $client = Client::find($request->client_id);
            $users = User::all()->except($request->auth->id);
            foreach ($users as $user) {
                $n = new Notification;
                $n->title = "Mise a jour de l'affectation(N°" . $c->id . ") véhicule: " . $car->matricule . ", pour Client: " . $client->designation;
                if ($driver) {
                    $n->sub_title = "Conducteur: " . $driver->firstname . " " . $driver->lastname;
                } else {
                    $n->sub_title = "Conducteur:  Sans Conducteur";
                }
                $n->url = "/affectations/show/" . $c->id;
                $n->from_id =  $request->auth->id;
                $n->to_id = $user->id;
                try {
                    $n->save();
                } catch (QueryException $e) {
                    $c->delete();
                    return new JsonResponse([
                        'message' => $e
                    ], Response::HTTP_BAD_REQUEST);
                }
            }
            return new JsonResponse([
                'message' => 'Success',
                'data' => $c
            ], Response::HTTP_CREATED);
        }
        return new JsonResponse([
            'message' => 'Permission denied'
        ], Response::HTTP_UNAUTHORIZED);
    }

    function restitition(Request $request, $id)
    {
        try {
            $cc = CarClient::find($id);
        } catch (QueryException $e) {
            return new JsonResponse([
                'message' => $e
            ], Response::HTTP_BAD_REQUEST);
        }

        $updated_at = new DateTime($cc->updated_at);
        $diff = (int)$updated_at->diff(new DateTime())->format('%h');
        if ($diff > 24 && $cc->date_restitition) {
            return new JsonResponse([
                'message' => 'Permission denied'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!$request->date_restitition) {
            $cc->date_restitition = null;
            $cc->motif_restitition = "";
            $cc->restitition_comment = null;
            try {
                $cc->save();
            } catch (QueryException $e) {
                return new JsonResponse([
                    'message' => $e
                ], Response::HTTP_BAD_REQUEST);
            }

            $car = Car::find($cc->car_id);
            $client = Client::find($cc->client_id);
            $users = User::all()->except($request->auth->id);

            foreach ($users as $user) {
                $n = new Notification;
                $n->title = "Annulation De Restitition De Véhicule: " . $car->matricule;
                $n->sub_title = "";
                $n->url = "";
                $n->from_id =  $request->auth->id;
                $n->to_id = $user->id;
                try {
                    $n->save();
                } catch (QueryException $e) {
                    //$cc->delete();
                    return new JsonResponse([
                        'message' => $e
                    ], Response::HTTP_BAD_REQUEST);
                }
            }
            return new JsonResponse([
                'message' => 'Success',
                'data' => $cc
            ], Response::HTTP_CREATED);
        }

        $this->validate($request, [
            'date_restitition' => 'required',
            'motif_restitition' => 'required',
            'restitition_comment' => 'required',
        ]);

        $cc->date_restitition = Carbon::parse($request->date_restitition);
        $cc->motif_restitition = $request->motif_restitition;
        $cc->restitition_comment = $request->restitition_comment;
        try {
            $cc->save();
        } catch (QueryException $e) {
            return new JsonResponse([
                'message' => $e
            ], Response::HTTP_BAD_REQUEST);
        }

        $car = Car::find($cc->car_id);
        $client = Client::find($cc->client_id);
        $users = User::all()->except($request->auth->id);

        foreach ($users as $user) {
            $n = new Notification;
            $n->title = "Restitition De Véhicule: " . $car->matricule;
            $n->sub_title = "À cause de: " . $cc->motif_restitition . "Date Restitition: " . $cc->date_restitition . " | " . $client->designation;
            $n->url = "/affectations/show/" . $cc->id;
            $n->from_id =  $request->auth->id;
            $n->to_id = $user->id;
            try {
                $n->save();
            } catch (QueryException $e) {
                //$cc->delete();
                return new JsonResponse([
                    'message' => $e
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        return new JsonResponse([
            'message' => 'Success',
            'data' => $cc
        ], Response::HTTP_CREATED);
        if ($request->motif_restitition === "panne") {
            // TODD: set car state to panne
            //$users = User::where('poste', 'mantenance')->except($request->auth->id);
        }
    }

    function get_ga_checklist($id, Request $request)
    {
    }
    function create_ga_checklist($id, Request $request)
    {
        $has_role_gm = false;
        $has_role_ga = false;
        $has_role_com = false;
        foreach ($request->auth->roles as $role) {
            if ($role->name === "ADD_EDIT_AFFECTATIONS_CLIENTS") {
                $has_role_com = true;
            }
            if ($role->name === "ADD_EDIT_AFFECTATIONS_DRIVERS_CHECK_LIST") {
                $has_role_gm = true;
            }
            if ($role->name === "ADD_EDIT_AFFECTATIONS_DRIVERS") {
                $has_role_ga = true;
            }
        }
        if ($has_role_ga) {
            $this->validate($request, [
                'driver_id' => 'required',
                'carte_grise' => 'required',
                'assurance' => 'required',
                'vignette' => 'required',
                'carte_gpl' => 'required',
                'permis_circuler' => 'required',
                'scanner' => 'required',
            ]);
            try {
                $row = CarClient::create([
                    'car_client_id' => $id,
                    'driver_id' => $request->driver_id,
                    'carte_grise' => $request->carte_grise,
                    'assurance' => $request->assurance,
                    'vignette' => $request->vignette,
                    'carte_gpl' => $request->carte_gpl,
                    'permis_circuler' => $request->permis_circuler,
                    'scanner' => $request->scanner,
                    'gm_comment' => $request->gm_comment,
                    'ga_id' => $request->auth->id
                ])->get()[0];
                $car_client = CarClient::find($id);
                $car = Car::find($car_client->car_id);
                dump($car);
                $users = User::where("poste", "GM")->get()->all();
                foreach ($users as $user) {
                    $n = new Notification;
                    $n->title = "Gestionnaier administratif: Affectation vehicule: " . $car->matricule;
                    $n->sub_title = "Check list materiel";
                    $n->url = "/carclients/" . $id . "/gm_checklist/"; // url for carClient ID
                    $n->from_id =  $request->auth->id;
                    $n->to_id = $user->id;
                    try {
                        $n->save();
                    } catch (QueryException $e) {
                        $row->delete();
                        return new JsonResponse([
                            'message' => $e
                        ], Response::HTTP_BAD_REQUEST);
                    }
                }
            } catch (QueryException $e) {
                return new JsonResponse([
                    'message' => $e
                ], Response::HTTP_BAD_REQUEST);
            }
        }
    }
}


// get latest client $c = App\Car::find(1)->with(['clients'=> function($q){$q->latest()->take(1);}])->get()