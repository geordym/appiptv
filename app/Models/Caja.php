<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    protected $table = "cajas";


    public function paquetes()
    {
        return $this->belongsToMany(Paquete::class, 'paquetes_cajas', 'caja_id', 'paquete_id');
    }

}
