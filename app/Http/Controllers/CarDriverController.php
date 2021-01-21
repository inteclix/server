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

use App\CarDriver;
use App\Driver;
use App\User;
use App\Notification;
use DateTime;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\TryCatch;

class CarDriverController extends Controller
{
    function get($id, Request $request)
    {
        try {
            $c = CarDriver::findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        $c->car = Car::find($c->car_id);
        $c->car->latestClient = $c->car->latestClient();
        $c->driver = Driver::find($c->driver_id);
        $c->createdby = User::find($c->createdby_id);
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
                $role->name === "ADD_EDIT_AFFECTATIONS_DRIVERS_CHECK_LIST"
            ) {
                $has_role = true;
            }
        }
        if ($has_role) {
            $all = CarDriver::orderBy('id', 'desc')->take(100)->get()->all();
            foreach ($all as $a) {
                $a->car = Car::find($a->car_id);
                $a->driver = Driver::find($a->driver_id);
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
            if ($role->name === "ADD_EDIT_AFFECTATIONS_DRIVERS_CHECK_LIST") {
                $has_role_com = true;
            }
            if ($role->name === "ADD_EDIT_AFFECTATIONS_DRIVERS_CHECK_LIST") {
                $has_role_gm = true;
            }
            if ($role->name === "ADD_EDIT_AFFECTATIONS_DRIVERS_CHECK_LIST") {
                $has_role_ga = true;
            }
        }
        if ($has_role_ga) {
            $this->validate($request, [
                'driver_id' => 'required',
                'car_id' => 'required',
                'date_affectation_driver' => 'required',
            ]);
            $cc = CarDriver::where('car_id', $request->car_id)->orderBy('created_at', 'desc')->first();
            if ($cc &&  !$cc->date_restitition_driver) {
                return new JsonResponse([
                    'message' => "Décharge deja affecte"
                ], Response::HTTP_BAD_REQUEST);
            }

            try {
                $carDriver = CarDriver::create([
                    'driver_id' => $request->driver_id,
                    'car_id' => $request->car_id,
                    'date_affectation_driver' => Carbon::parse($request->date_affectation_driver),

                    'carte_grise' => $request->carte_grise,
                    'assurance' => $request->assurance,
                    'vignette' => $request->vignette,
                    'carte_gpl' => $request->carte_gpl,
                    'permis_circuler' => $request->permis_circuler,
                    'scanner' => $request->scanner,
                    'roue_secour' => $request->roue_secour,
                    'cric' => $request->cric,
                    'poste_radio' => $request->poste_radio,
                    'cle_roue' => $request->cle_roue,
                    'cle_vehicule' => $request->cle_vehicule,
                    'km' => $request->km,
                    'affectation_comment' => $request->affectation_comment,
                    'createdby_id' => $request->auth->id,
                ])->get()[0];
            } catch (QueryException $e) {
                return new JsonResponse([
                    'message' => $e
                ], Response::HTTP_BAD_REQUEST);
            }

            //$c->
            //   dump($carDriver);
            $car = Car::find($request->car_id);
            $driver = Driver::find($request->driver_id);
            $users = User::all()->except($request->auth->id);
            foreach ($users as $user) {
                $n = new Notification;
                $n->title = "Nouvelle Décharge Vehicule: " . $car->matricule;
                $n->sub_title = "Conducteur: " . $driver->firstname . " " . $driver->lastname;
                $n->url = "/decharges/show/" . $carDriver->id;
                $n->from_id =  $request->auth->id;
                $n->to_id = $user->id;
                try {
                    $n->save();
                } catch (QueryException $e) {
                    $carDriver->delete();
                    return new JsonResponse([
                        'message' => $e
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            return new JsonResponse([
                'message' => 'Success',
                'data' => $carDriver
            ], Response::HTTP_CREATED);
        }
        return new JsonResponse([
            'message' => 'Permission denied'
        ], Response::HTTP_UNAUTHORIZED);
    }

    function delete($id, Request $request)
    {
        try {
            $carDriver = CarDriver::findOrFail($id);
        } catch (Exception $e) {
            return new JsonResponse([
                'message' => 'Id d\'ont exist',
            ], Response::HTTP_BAD_REQUEST);
        }
        dump($carDriver);
        if($carDriver->car()->latestDriver()->id !== $carDriver->driver_id){
            return new JsonResponse([
                'message' => 'Vehicule deja affecté a autre chauffeur'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $created_at = new DateTime($carDriver->created_at);
        $diff = (int)$created_at->diff(new DateTime())->format('%h');
        if ($diff > 24) {
            return new JsonResponse([
                'message' => 'Permission denied'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $has_role = true;
        foreach ($request->auth->roles as $role) {
            if ($role->name === "ADD_EDIT_AFFECTATIONS_DRIVERS_CHECK_LIST") {
                $has_role = true;
            }
        }
        if ($has_role) {
            CarDriver::destroy($id);
            $car = Car::find($carDriver->car_id);
            $driver = Driver::find($carDriver->driver_id);
            $users = User::all()->except($request->auth->id);
            foreach ($users as $user) {
                $n = new Notification;
                $n->title = "Suppression De Décharge Vehicule: " . $car->matricule;
                $n->sub_title = "Conducteur: " . $driver->firstname . " " . $driver->lastname;
                $n->url = "/decharges/show/" . $carDriver->id;
                $n->from_id =  $request->auth->id;
                $n->to_id = $user->id;
                try {
                    $n->save();
                } catch (QueryException $e) {
                    $carDriver->delete();
                    return new JsonResponse([
                        'message' => $e
                    ], Response::HTTP_BAD_REQUEST);
                }
            }
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
            $c = CarDriver::findOrFail($id);
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
            if ($role->name === "ADD_EDIT_AFFECTATIONS_DRIVERS_CHECK_LIST") {
                $has_role = true;
            }
        }
        if ($has_role) {
            $c->driver_id = $request->driver_id;
            $c->car_id = $request->car_id;
            $c->date_affectation_driver = Carbon::parse($request->date_affectation_driver);

            $c->carte_grise = $request->carte_grise;
            $c->assurance = $request->assurance;
            $c->vignette = $request->vignette;
            $c->carte_gpl = $request->carte_gpl;
            $c->permis_circuler = $request->permis_circuler;
            $c->scanner = $request->scanner;
            $c->roue_secour = $request->roue_secour;
            $c->cric = $request->cric;
            $c->poste_radio = $request->poste_radio;
            $c->cle_roue = $request->cle_roue;
            $c->cle_vehicule = $request->cle_vehicule;
            $c->km = $request->km;

            $c->affectation_comment = $request->affectation_comment;

            $c->updatedby_id = $request->auth->id;
            try {
                $c->save();
            } catch (QueryException $e) {
                return new JsonResponse([
                    'message' => $e
                ], Response::HTTP_BAD_REQUEST);
            }
            $car = Car::find($request->car_id);
            $driver = Driver::find($request->driver_id);
            $driver = Driver::find($request->driver_id);
            $users = User::all()->except($request->auth->id);
            foreach ($users as $user) {
                $n = new Notification;
                $n->title = "Mise A Jour de Décharge(N°" . $c->id . ") véhicule: " . $car->matricule . ", pour Driver: " . $driver->designation;
                $n->sub_title = "Conducteur: " . $driver->firstname . "  " . $driver->lastname;
                $n->url = "/decharges/show/" . $c->id;
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
            $cc = CarDriver::find($id);
        } catch (QueryException $e) {
            return new JsonResponse([
                'message' => $e
            ], Response::HTTP_BAD_REQUEST);
        }
        if(!$request->date_restitition_driver &&  $cc->car->latestDriver() && $cc->car->latestDriver()->id !== $cc->driver_id){
            return new JsonResponse([
                'message' => 'Vehicule deja affecté a autre chauffeur'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $updated_at = new DateTime($cc->updated_at);
        $diff = (int)$updated_at->diff(new DateTime())->format('%h');
        if ($diff > 24 && $cc->date_restitition_driver) {
            return new JsonResponse([
                'message' => 'Permission denied'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!$request->date_restitition_driver) {
            $cc->date_restitition_driver = null;
            $cc->motif_restitition_driver = "";
            $cc->km_restitition = null;
            $cc->restitition_comment = null;

            $cc->carte_grise_restitition = null;
            $cc->assurance_restitition = null;
            $cc->vignette_restitition = null;
            $cc->carte_gpl_restitition = null;
            $cc->permis_circuler_restitition = null;
            $cc->scanner_restitition = null;
            $cc->roue_secour_restitition = null;
            $cc->cric_restitition = null;
            $cc->poste_radio_restitition =  null;
            $cc->cle_roue_restitition = null;
            $cc->cle_vehicule_restitition = null;

            try {
                $cc->save();
            } catch (QueryException $e) {
                return new JsonResponse([
                    'message' => $e
                ], Response::HTTP_BAD_REQUEST);
            }

            $car = Car::find($cc->car_id);
            $driver = Driver::find($cc->driver_id);
            $users = User::all()->except($request->auth->id);

            foreach ($users as $user) {
                $n = new Notification;
                $n->title = "Annulation De Restitition De Décharge Véhicule: " . $car->matricule;
                $n->sub_title = "Conducteur " . $driver->firstname . "" . $driver->lastname;
                $n->url = "/decharges/show/" . $cc->id;
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
            'date_restitition_driver' => 'required',
            'motif_restitition_driver' => 'required',
            'km_restitition' => 'required',
            'restitition_comment' => 'required',
        ]);
        $cc->date_restitition_driver = Carbon::parse($request->date_restitition_driver);
        $cc->motif_restitition_driver = $request->motif_restitition_driver;
        $cc->km_restitition = $request->km_restitition;
        $cc->restitition_comment = $request->restitition_comment;
        
        $cc->carte_grise_restitition = $request->carte_grise_restitition;
        $cc->assurance_restitition = $request->assurance_restitition;
        $cc->vignette_restitition = $request->vignette_restitition;
        $cc->carte_gpl_restitition = $request->carte_gpl_restitition;
        $cc->permis_circuler_restitition = $request->permis_circuler_restitition;
        $cc->scanner_restitition = $request->scanner_restitition;
        $cc->roue_secour_restitition = $request->roue_secour_restitition;
        $cc->cric_restitition = $request->cric_restitition;
        $cc->poste_radio_restitition = $request->poste_radio_restitition;
        $cc->cle_roue_restitition = $request->cle_roue_restitition;
        $cc->cle_vehicule_restitition = $request->cle_vehicule_restitition;

        try {
            $cc->save();
        } catch (QueryException $e) {
            return new JsonResponse([
                'message' => $e
            ], Response::HTTP_BAD_REQUEST);
        }

        $car = Car::find($cc->car_id);
        $driver = Driver::find($cc->driver_id);
        $users = User::all()->except($request->auth->id);

        foreach ($users as $user) {
            $n = new Notification;
            $n->title = "Restitition De Décharge Véhicule: " . $car->matricule;
            $n->sub_title = "À cause de: " . $cc->motif_restitition_driver . "Date Restitition: " . $cc->date_restitition_driver;
            $n->url = "/decharges/show/" . $cc->id;
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
        if ($request->motif_restitition_driver === "panne") {
            // TODD: set car state to panne
            //$users = User::where('poste', 'mantenance')->except($request->auth->id);
        }
    }
}


// get latest driver $c = App\Car::find(1)->with(['drivers'=> function($q){$q->latest()->take(1);}])->get()