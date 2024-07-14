<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paquete extends Model
{
    use HasFactory;
    protected $table = 'paquetes';


       // Relación de muchos a muchos con Caja
    public function cajas()
    {
        return $this->belongsToMany(Caja::class, 'paquetes_cajas', 'paquete_id', 'caja_id');
    }

      // Relación de Paquete con Canal
      public function canales()
      {
        return $this->belongsToMany(Canal::class, 'paquetes_canales', 'paquete_id', 'canal_id');
      }

      public function getCanalesCountAttribute()
      {
          return $this->canales()->count();
      }
}
