<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Farmer extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'farmer_monkharit' => 'boolean',
        'farmer_morabi_card' => 'boolean',
        'mo3idat_elect' => 'boolean',
        'mo3idat_gaz' => 'boolean',
        'mo3idat_eau' => 'boolean',
        'mo3idat_khazan_eau' => 'boolean',
        'mo3idat_madba7_wilaya' => 'boolean',
        'tagdiya_3ilaf_mada_idafiya' => 'boolean',
        'ri3aya_mantojat_saydalaniya' => 'boolean',
        'ri3aya_tamtalik_i3timad_si7i' => 'boolean',
        'tasswik_morakib_si7i_dab7' => 'boolean',
        'tasswik_bay3_montadam_nafss_kamiya' => 'boolean',
        'tasswik_ishar_li_mantojk' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
