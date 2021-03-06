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
use App\User;
use App\Notification;
use App\Restitition;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\TryCatch;
use App\CarState;

class DechargeController extends Controller
{

    function getChecklistInstance(Request $request)
    {
        $checklist = new Checklist;
        $checklist->car_id = $request->car_id;
        $checklist->driver_id = $request->driver_id;
        $checklist->createdby_id = $request->auth->id;
        $checklist->niveau_carburant = $request->niveau_carburant;
        $checklist->odometre = $request->odometre;
        $checklist->starts = $request->starts;
        $checklist->carte_grise = $request->carte_grise;
        $checklist->assurance = $request->assurance;
        $checklist->assurance_marchandises = $request->assurance_marchandises;
        $checklist->scanner = $request->scanner;
        $checklist->permis_circuler = $request->permis_circuler;
        $checklist->carnet_enter = $request->carnet_enter;
        $checklist->vignette = $request->vignette;
        $checklist->carte_gpl = $request->carte_gpl;
        $checklist->gillet = $request->gillet;
        $checklist->roue_secour = $request->roue_secour;
        $checklist->cric = $request->cric;
        $checklist->poste_radio = $request->poste_radio;
        $checklist->cle_roue = $request->cle_roue;
        $checklist->extincteur = $request->extincteur;
        $checklist->boite_pharm = $request->boite_pharm;
        $checklist->triangle = $request->triangle;
        $checklist->pochette_cle = $request->pochette_cle;
        $checklist->cle_vehicule = $request->cle_vehicule;
        $checklist->date_checklist = $request->date_checklist;
        $checklist->observation = $request->observation_checklist;
        return $checklist;
    }

    function createChecklist(Request $request)
    {
        $this->validate($request, [
            "car_id" => "required",
            "driver_id" => "required",
            "niveau_carburant" => "required",
            "odometre" =>  "required",
            "starts" => "required",
            "cle_vehicule" => "required",
        ]);
    }

    function createDecharge(Request $request)
    {
        $this->validate($request, [
            "client_id" => "required",
            "car_id" => "required",
            "date_decharge" => "required",
            "driver_id" => "required",
            "niveau_carburant" => "required",
            "odometre" =>  "required",
            "starts" => "required",
            "cle_vehicule" => "required",
        ]);
        // check if car has decharge without restitition
        $car = Car::find($request->get("car_id"));
        $checklistsDecharges = $car->decharges()->latest()->get()->all();
        if (count($checklistsDecharges) > 0) {
            $latestCheckListDecharge = $checklistsDecharges[0];
            //dump($latestCheckListDecharge);
            $row = DB::table("decharges")
                ->join("checklists", "decharges.id", "=", "checklists.decharge_id")
                ->join('restititions', 'decharges.id', '=', "restititions.decharge_id")
                ->join("cars", "checklists.car_id", "=", "cars.id")
                ->select([
                    'decharges.id as decharges.id',
                    'restititions.id as restititions.id',
                ])
                ->where('checklists.id', '=', $latestCheckListDecharge->id)
                ->get()->all();
            // dump($row);
            if (count($row) === 0) {
                return $this->error("Véhicule déja affecté !");
            }
        }

        // create instance decharge
        $decharge = new Decharge;
        $decharge->client_id = $request->client_id;
        $decharge->date_decharge = $request->date_decharge;
        $decharge->date_fin_prestation = $request->date_fin_prestation;
        try {
            $decharge->save();
        } catch (QueryException $e) {
            $decharge->delete();
            return $this->error("Error !");
        }
        // create instance decharge
        $checklist = $this->getChecklistInstance($request);
        $checklist->decharge_id = $decharge->id;

        try {
            $checklist->save();
        } catch (QueryException $e) {
            $checklist->delete();
            $decharge->delete();
            dump($e);
            return $this->error("error");
        }
        $car = Car::find($request->car_id);
        $users = $car->users()->get();
        foreach ($users as $user) {
            if ($user->id === $request->auth->id) {
                continue;
            }
            $n = new Notification;
            $n->title = "Nouveau décharge pour vehicule: " . $car->code_gps . " | " . $car->matricule;
            $n->sub_title = "par " . $request->auth->username . ", " . $decharge->created_at;
            $n->url = "/decharges/show/" . $decharge->id;
            $n->from_id = $request->auth->id;
            $n->to_id = $user->id;
            $n->type = "decharge";
            $n->type_id = $decharge->id;
            try {
                $n->save();
            } catch (QueryException $e) {
            }
        }

        return $this->success($decharge);
    }

    function getDecharges(Request $request)
    {
        $sort = $request->get("sort") === "ascend" ? "asc" : "desc";
        $sortBy = $request->get("sortBy") ? $request->get("sortBy") : "id";
        $current = $request->get("current") ? $request->get("current") : 1;
        $pageSize = $request->get("pageSize") ? $request->get("pageSize") : 20;
        $decharges =  DB::table("decharges")
            ->join('checklists', function ($join) {
                $join->on('decharges.id', '=', 'checklists.decharge_id')
                    ->on('checklists.id', '=', DB::raw("(select max(id) from checklists WHERE checklists.decharge_id = decharges.id)"));
            })
            ->join('clients', 'decharges.client_id', '=', 'clients.id')
            ->leftJoin('clients as clients_mother', 'clients.client_id', '=', 'clients_mother.id')
            ->leftJoin('restititions', 'decharges.id', '=', "restititions.decharge_id")
            ->join("cars", "checklists.car_id", "=", "cars.id")
            ->join("car_user", "cars.id", "car_user.car_id")
            ->join("drivers", "checklists.driver_id", "=", "drivers.id")
            ->join("users as creates", "checklists.createdby_id", "=", "creates.id")
            ->leftJoin("users as acceptes", "decharges.acceptedby_id", "=", "acceptes.id")
            ->join("users as owners", "car_user.user_id", "=", "owners.id")
            ->select([
                'restititions.id as restititions.id',
                'decharges.id as id',
                'cars.matricule as cars_matricule',
                'checklists.car_id as car_id',
                DB::raw("CONCAT(drivers.firstname, ' ',drivers.lastname) as drivers_fullname"),
                'code_gps',
                'clients.designation as clients_designation',
                'clients_mother.designation as clients_mother_designation',
                'decharges.date_decharge as date_decharge',
                'date_fin_prestation',
                'creates.username as createdby_username',
                'acceptes.username as acceptedby_username',
                'owners.id as ownerId'
            ])
            ->where("owners.id", "=", $request->auth->id)
            ->where('cars.matricule', 'like', "%{$request->get("cars_matricule")}%")
            ->where('cars.code_gps', 'like', "%{$request->get("code_gps")}%")
            ->where('clients.designation', 'like', "%{$request->get("clients_designation")}%")
            //->where('clients_mother.designation', 'like', "%{$request->get("clients_designation")}%")
            ->where('restititions.id', '=', null)
            ->orderBy($sortBy, $sort)
            ->paginate(
                $pageSize, // per page (may be get it from request)
                ['*'], // columns to select from table (default *, means all fields)
                'page', // page name that holds the page number in the query string
                $current // current page, default 1
            );

        return $decharges;
    }

    function getDechargesRestititions(Request $request)
    {
        $sort = $request->get("sort") === "ascend" ? "asc" : "desc";
        $sortBy = $request->get("sortBy") ? $request->get("sortBy") : "id";
        $current = $request->get("current") ? $request->get("current") : 1;
        $pageSize = $request->get("pageSize") ? $request->get("pageSize") : 20;
        if ($request->auth->username === "admin") {
            return DB::table("decharges")
                ->join('checklists', function ($join) {
                    $join->on('decharges.id', '=', 'checklists.decharge_id')
                        ->on('checklists.id', '=', DB::raw("(select max(id) from checklists WHERE checklists.decharge_id = decharges.id)"));
                })
                ->join('clients', 'decharges.client_id', '=', 'clients.id')
                ->leftJoin('clients as clients_mother', 'clients.client_id', '=', 'clients_mother.id')
                ->leftJoin('restititions', 'decharges.id', '=', "restititions.decharge_id")
                ->join("cars", "checklists.car_id", "=", "cars.id")
                ->join("users", "checklists.createdby_id", "=", "users.id")
                ->join("drivers", "checklists.driver_id", "=", "drivers.id")
                ->select([
                    'restititions.id as restititions.id',
                    'restititions.date_restitition as restititions.date_restitition',
                    'decharges.id as id',
                    'cars.matricule as cars_matricule',
                    'checklists.car_id as car_id',
                    DB::raw("CONCAT(drivers.firstname, ' ',drivers.lastname) as drivers_fullname"),
                    'code_gps',
                    'clients.designation as clients_designation',
                    'clients_mother.designation as clients_mother_designation',
                    'decharges.date_decharge as date_decharge',
                    'date_fin_prestation',
                    'username'
                ])
                ->orderBy($sortBy, $sort)
                ->where('cars.matricule', 'like', "%{$request->get("cars_matricule")}%")
                ->where('cars.code_gps', 'like', "%{$request->get("code_gps")}%")
                ->where('clients.designation', 'like', "%{$request->get("clients_designation")}%")
                ->where('restititions.id', '<>', null)
                ->paginate(
                    $pageSize, // per page (may be get it from request)
                    ['*'], // columns to select from table (default *, means all fields)
                    'page', // page name that holds the page number in the query string
                    $current // current page, default 1
                );
        } else {
            return DB::table("decharges")
                ->join('checklists', function ($join) {
                    $join->on('decharges.id', '=', 'checklists.decharge_id')
                        ->on('checklists.id', '=', DB::raw("(select max(id) from checklists WHERE checklists.decharge_id = decharges.id)"));
                })
                ->join('clients', 'decharges.client_id', '=', 'clients.id')
                ->leftJoin('restititions', 'decharges.id', '=', "restititions.decharge_id")
                ->join("cars", "checklists.car_id", "=", "cars.id")
                ->join("car_user", "cars.id", "car_user.car_id")
                ->join("users as owners", "car_user.user_id", "=", "owners.id")
                ->join("users", "checklists.createdby_id", "=", "users.id")
                ->join("drivers", "checklists.driver_id", "=", "drivers.id")
                ->select([
                    'restititions.id as restititions.id',
                    'restititions.date_restitition as restititions.date_restitition',
                    'decharges.id as id',
                    'cars.matricule as cars_matricule',
                    'checklists.car_id as car_id',
                    DB::raw("CONCAT(drivers.firstname, ' ',drivers.lastname) as drivers_fullname"),
                    'code_gps',
                    'designation as clients_designation',
                    'decharges.date_decharge as date_decharge',
                    'date_fin_prestation',
                    'users.username as username',
                ])
                ->orderBy($sortBy, $sort)
                ->where("owners.id", "=", $request->auth->id)
                ->where('matricule', 'like', "%{$request->get("matricule")}%")
                ->where('restititions.id', '<>', null)
                ->paginate(
                    $pageSize, // per page (may be get it from request)
                    ['*'], // columns to select from table (default *, means all fields)
                    'page', // page name that holds the page number in the query string
                    $current // current page, default 1
                );
        }
    }

    function get($id, Request $request)
    {
        $decharge = DB::table("decharges")
            ->join('clients', 'decharges.client_id', '=', 'clients.id')
            ->leftJoin('clients as clients_mother', 'clients.client_id', '=', 'clients_mother.id')
            ->leftJoin('restititions', 'decharges.id', '=', "restititions.decharge_id")
            ->join("checklists", "decharges.id", "=", "checklists.decharge_id")
            ->join("cars", "checklists.car_id", "=", "cars.id")
            ->join("drivers", "checklists.driver_id", "=", "drivers.id")
            ->join("users", "checklists.createdby_id", "=", "users.id")
            ->select([
                'decharges.id as decharges.id',
                'decharges.date_decharge as decharges.date_decharge',
                'decharges.date_fin_prestation',
                'checklists.id as decharges.checklist_id',
                // 'decharges.checklists as checklist',
                'restititions.id as restititions_id',
                'users.username as users.username',
                DB::raw("CONCAT(drivers.firstname, ' ',drivers.lastname) as drivers_fullname"),
                'cars.id as cars.id',
                'cars.id as car_id',
                'marque as cars.marque',
                'matricule as cars.matricule',
                'code_gps as cars.code_gps',
                'niveau_carburant',
                'odometre',
                'starts',
                'carte_grise',
                'assurance',
                'assurance_marchandises',
                'scanner',
                'permis_circuler',
                'carnet_enter',
                'vignette',
                'carte_gpl',
                'gillet',
                'roue_secour',
                'cric',
                'poste_radio',
                'cle_roue',
                'extincteur',
                'boite_pharm',
                'triangle',
                'pochette_cle',
                'cle_vehicule',
                'checklists.observation as observation_checklist',
                'checklists.date_checklist as date_checklist',
                'drivers.id as drivers.id',
                'drivers.id as driver_id',
                'drivers.firstname as drivers.firstname',
                'drivers.lastname as drivers.lastname',
                'drivers.tel as drivers.tel',
                'clients.designation as clients.designation',
                'clients_mother.designation as clients_mother_designation',
                'clients.id as client_id',
                'clients.id as clients.id',
            ])
            ->where('decharges.id', '=', $id)
           // ->orderBy('checklists.id', 'DESC')
            ->get();
        if (count($decharge) < 1) {
            return $this->error("Not found");
        }
        $d = $decharge[0];
        //dump($d->restititions_id);
        if ($d->restititions_id) {
            $r = Restitition::find($d->restititions_id);
            $r->checklist = $r->checklist()->get()->all()[0];
            $d->restitition = $r;
            //dump($d->restitition);
        }
        $d->client = Client::find($d->client_id);
        $d->car = Car::find($d->car_id);
        $d->driver = Driver::find($d->driver_id);
        $d->checklists = Decharge::find($id)->checklists()->with('driver')->with('car')->get();
        return $this->http_ok($d);
    }

    function accepteDecharge(Request $request, $id)
    {
        try {
            $decharge = Decharge::find($id);
        } catch (QueryException $e) {
            return $this->http_bad();
        }
        if ($this->hasRole($request, "VALIDER_DECHARGE")) {
            $decharge->acceptedby_id = $request->auth->id;
            try {
                $decharge->save();
            } catch (QueryException $e) {
                return $this->http_bad();
            }
            return $this->http_ok($decharge);
        }
        return $this->http_unauthorized();
    }

    function update(Request $request, $id)
    {
        $this->validate($request, [
            "client_id" => "required",
            "car_id" => "required",
            "date_decharge" => "required",
            "driver_id" => "required",
            "niveau_carburant" => "required",
            "odometre" =>  "required",
            "starts" => "required",
            "cle_vehicule" => "required",
        ]);
        try {
            $decharge = Decharge::find($id);
        } catch (QueryException $e) {
            return $this->http_bad();
        }
        if ($this->hasRole($request, "MODIFIER_DECHARGE")) {
            // verifier if decharge has one checklist
            $checklists = $decharge->checklists()->get();
            if (count($checklists) == 1) {
                $checklist = $checklists[0];
                if ($request->auth->id !== $checklist->createdby_id) {
                    return $this->http_unauthorized();
                }
                // update decharge
                $decharge->client_id = $request->client_id;
                $decharge->date_decharge = $request->date_decharge;
                $decharge->date_fin_prestation = $request->date_fin_prestation;
                $decharge->acceptedby_id = null;
                try {
                    $decharge->save();
                } catch (QueryException $e) {
                    return $this->http_bad();
                }

                // update checklist

                $checklist->car_id = $request->car_id;
                $checklist->driver_id = $request->driver_id;
                //$checklist->updatedby_id = $request->auth->id;
                $checklist->niveau_carburant = $request->niveau_carburant;
                $checklist->odometre = $request->odometre;
                $checklist->starts = $request->starts;
                $checklist->carte_grise = $request->carte_grise;
                $checklist->assurance = $request->assurance;
                $checklist->assurance_marchandises = $request->assurance_marchandises;
                $checklist->scanner = $request->scanner;
                $checklist->permis_circuler = $request->permis_circuler;
                $checklist->carnet_enter = $request->carnet_enter;
                $checklist->vignette = $request->vignette;
                $checklist->carte_gpl = $request->carte_gpl;
                $checklist->gillet = $request->gillet;
                $checklist->roue_secour = $request->roue_secour;
                $checklist->cric = $request->cric;
                $checklist->poste_radio = $request->poste_radio;
                $checklist->cle_roue = $request->cle_roue;
                $checklist->extincteur = $request->extincteur;
                $checklist->boite_pharm = $request->boite_pharm;
                $checklist->triangle = $request->triangle;
                $checklist->pochette_cle = $request->pochette_cle;
                $checklist->cle_vehicule = $request->cle_vehicule;
                $checklist->date_checklist = $request->date_checklist;
                $checklist->observation = $request->observation_checklist;
                try {
                    $checklist->save();
                } catch (QueryException $e) {
                    return $this->http_bad();
                }
                return $this->http_ok($decharge);
            }
            return $this->http_unauthorized("On peut pas modifier ce décharge");
        }
        return $this->http_unauthorized();
    }

    function addChecklist(Request $request, $id)
    {
        $this->validate($request, [
            "decharge_id" => "required",
            "driver_id" => "required",
            "car_id" => "required",
            "niveau_carburant" => "required",
            "odometre" =>  "required",
            "starts" => "required",
            "cle_vehicule" => "required",
        ]);
        $decharge = Decharge::find($id);
        if (!$decharge) {
            return $this->error("Aucun decharge trouver");
        }
        $checklist = $this->getChecklistInstance($request);
        $checklist->decharge_id = $decharge->id;
        try {
            $checklist->save();
        } catch (QueryException $e) {
            return $this->http_bad();
        }
    }

    function deleteChecklist(Request $request, $id)
    {
        $checklist = Checklist::find($id);
        if (!$checklist) {
            return $this->error("Aucun decharge trouver");
        }
        try {
            $checklist->delete();
            return $this->success([], "Supprimer avec success");
        } catch (QueryException $e) {
            return $this->error("Error !");
        }
    }
    function restitition(Request $request, $id)
    {
        $this->validate($request, [
            "car_id" => "required",
            "date_restitition" => "required",
            "motif_restitition" => "required",
        ]);
        // check if is restituer
        $r = Decharge::find($id)->restitition()->get()->all();
        if (count($r) !== 0) {
            return $this->error("Véhicule déja restituer !");
        }

        $checklist = $this->getChecklistInstance($request);
        try {
            $checklist->save();
        } catch (QueryException $e) {
            $checklist->delete();
            //dump($e);
            return $this->http_bad();
        }
        $restitition = new Restitition;
        $restitition->decharge_id = $id;
        $restitition->checklist_id = $checklist->id;
        $restitition->date_restitition = $request->get("date_restitition");
        $restitition->motif_restitition = $request->get("motif_restitition");
        $restitition->createdby_id = $request->auth->id;
        try {
            $restitition->save();
        } catch (QueryException $e) {
            $restitition->delete();
            return $this->http_bad();
        }
        if ($restitition->motif_restitition === "REMPLACEMENT VEHICULE (VEHICULE EN PANNE)") {
            $c = new CarState;
            $c->car_id = $request->car_id;
            $c->name = "EN PANNE PARC";
            $c->state_date = $restitition->date_restitition;
            $c->observation = "";
            $c->createdby_id = $request->auth->id;
            try {
                $c->save();
            } catch (QueryException $e) {
                return $this->http_bad();
            }
            $car = Car::find($request->car_id);
            $users = $car->users()->get();
            foreach ($users as $user) {
                if ($user->id === $request->auth->id) {
                    continue;
                }
                $n = new Notification;
                $n->title = "Statu de vehicule: " . $car->code_gps . " | " . $car->matricule;
                $n->sub_title = $c->name . " ,par " . $request->auth->username;
                $n->url = "/cars_state/";
                $n->from_id = $request->auth->id;
                $n->to_id = $user->id;
                $n->type = "car_state_restitution";
                $n->type_id = $restitition->id;
                try {
                    $n->save();
                } catch (QueryException $e) {
                }
            }
        }
        return $this->success($restitition);
    }

    function deleteRestitition(Request $request, $id)
    {
        if ($this->hasRole($request, "SUPPRIMER_DECHARGE_RESTIUER")) {
            try {
                $r = Restitition::find($id);
            } catch (QueryException $e) {
                return $this->http_bad();
            }
            $r->checklist()->get()->all()[0];
            $ch = $r->checklist()->get()->all()[0];
            if ($ch->createdby_id === $request->auth->id) {
                $r->delete();
                $ch->delete();
                $ns = Notification::where("type", "=", "car_state_restitution")->where("type_id", "=", $id)->get()->all();
                foreach ($ns as $n) {
                    $n->delete();
                }
                return $this->success([], "Bien supprimer");
            }
            return $this->error("Non supprimer");
        }
        return $this->http_unauthorized();
    }

    function deleteDecharge(Request $request, $id)
    {
        $decharge = DB::table("decharges")
            ->join("checklists", "decharges.id", "=", "checklists.decharge_id")
            ->select([
                'decharges.id as decharges.id',
                'checklists.id as decharges.checklist_id',
                // 'decharges.checklists as checklist',
                'createdby_id',
            ])
            ->where('decharges.id', '=', $id)
            ->get();
        if (count($decharge) > 0 && $decharge[0]->createdby_id == $request->auth->id) {

            $d = Decharge::find($id);
            $ch = $d->checklists()->get()->all();
            foreach ($ch as $c) {
                $c->delete();
            }
            $d->delete();

            $ns = Notification::where("type", "=", "decharge")->where("type_id", "=", $id)->get()->all();
            foreach ($ns as $n) {
                $n->delete();
            }
            return $this->success([], "Bien supprimer");
        }



        return $this->error("Non Supprimer");
    }
}
