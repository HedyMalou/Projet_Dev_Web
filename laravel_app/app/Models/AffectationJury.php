<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffectationJury extends Model
{
    protected $table = 'AFFECTATION_JURY';
    public $timestamps = false;

    protected $fillable = ['id_jury', 'id_candidature', 'date_affectation'];

    protected $casts = [
        'date_affectation' => 'datetime',
    ];

    public function jury()
    {
        return $this->belongsTo(Jury::class, 'id_jury');
    }

    public function candidature()
    {
        return $this->belongsTo(Candidature::class, 'id_candidature');
    }
}
