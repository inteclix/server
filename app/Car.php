<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = [
        'matricule',
        'prop',
        'old_matricule',
        'code_gps',
        'genre',
        'marque',
        'type',
        'puissance',
        'energie',
        'carrosserie',
        'color',
        'createdby_id'
    ];
    protected $casts = [
        'id' => 'integer',
    ];
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'createdby_id');
    }

    public function users(){
        return $this->belongsToMany("App\User");
    }
    
    public function states()
    {
        return $this->hasMany('App\CarState');
    }

    public function latestState()
    {
        $states = $this->states()->latest()->get()->all();
        if (count($states) > 0) {
            return $states[0];
        }
        return null;
    }

    public function checklists()
    {
        return $this->hasMany('App\Checklist');
    }

    public function decharges()
    {
        return $this->hasMany('App\Checklist')->with("decharge")->whereHas("decharge");
    }
    //DB::table("decharges")->join("checklists", "decharges.id","=", "checklists.decharge_id")->select("decharges.*", "checklists.*")->get()
    public function dechargesShouldRestitition(){
        return $this->hasMany('App\Checklist')->whereHas("decharge",function($query){
                $query->where("date_fin_prestation", ">=", date("Y-m-d"));
            }
        );
    }

    public function notInDecharge(){
        return $this->hasMany('App\Checklist')->whereHas('restitition', function($query){
            $query->where("decharge_id", "<>", "");
        });
    }

    public function drivers()
    {
        return $this->belongsToMany('App\Driver', 'car_driver');
    }

    public function latestClient()
    {
        $clients = $this->clients()->latest()->withPivot(["id", "createdby_id", "date_affectation", "date_restitition"])->get()->all();
        if (count($clients) > 0) {
            $client = $clients[0];
            $client->pivot = $client->pivot;
            if ($client->pivot->date_restitition) {
                return null;
            }
            return $client;
        }
        return null;
    }

    public function clienlt($d)
    {
        $date1 = $d->date_affectation_driver;
        $date2 = $d->date_restitition_driver;

        $all = CarClient::orderBy('id', 'desc')
            ->where('date_affectation', '<=', "{$date2}")
            ->where(function ($q) use ($date1) {
                $q->where('date_restitition', '>=', "{$date1}")
                    ->orWhere('date_restitition', '=', null);
            })
            ->get()->all();
    }
    public function latestDriver()
    {
        $drivers = $this->drivers()->latest()->withPivot(["id", "createdby_id", "date_affectation_driver", "date_restitition_driver"])->get()->all();
        if (count($drivers) > 0) {
            $driver = $drivers[0];
            $driver->pivot = $driver->pivot;
            if ($driver->pivot->date_restitition_driver) {
                return null;
            }
            return $driver;
        }
        return null;
    }

    public function beforeLatestClient()
    {
        $clients = $this->clients()->latest()->withPivot(["id", "ga_id", "gm_id", "date_affectation", "date_restitution"])->get()->all();
        if (count($clients) > 0) {
            $client = $clients[0];
            $client->pivot = $client->pivot;
            if ($client->pivot->date_restitution) {
                return null;
            }
            return $client;
        }
        return null;
    }
}
