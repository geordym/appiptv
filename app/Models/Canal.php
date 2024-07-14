<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Canal extends Model
{
    use HasFactory;

    protected $table = 'canales';

    protected $fillable = [
        'key',
        'value',
        'type',
        'number'
    ];

    public function paquetes()
    {
        return $this->belongsToMany(Paquete::class, 'paquetes_canales', 'canal_id', 'paquete_id');
    }


}
