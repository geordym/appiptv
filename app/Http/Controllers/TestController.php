<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Paquete;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //

    public function test(){

       /* $paquete = new Paquete();
        $paquete->nombre = "normal2s";
        $paquete->save();*/

        $caja = Caja::with('paquetes', 'paquetes.canales')->where('id', 5)->first();
        //$caja->paquetes()->attach($paquete->id);

        return json_encode($caja);
    }
}
