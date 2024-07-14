<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Canal;
use App\Models\Paquete;
use Illuminate\Http\Request;

class PaqueteController extends Controller
{
    //

    public function index()
    {
        $paquetes = Paquete::all();
        return view('paquetes.index')->with('paquetes', $paquetes);
    }

    public function create()
    {
        return view('paquetes.create');
    }

    public function store(Request $request)
    {
        $nombre = $request->input('nombre');

        $paquete = new Paquete();
        $paquete->nombre = $nombre;
        $paquete->save();

        return redirect()->route('admin.paquetes.index')->with('success', 'Paquete creado correctamente.');
    }

    public function edit($id)
    {

        $canales = Canal::all();
        $paquete = Paquete::with(['canales'])->where('id', $id)->first();
        $paquete_canales = $paquete->canales;


        $canales_filtrados = $canales->diff($paquete_canales);

        return view('paquetes.edit')->with('canales', $canales_filtrados)->with('paquete', $paquete)
        ->with('paquete_canales', $paquete_canales)
        ;

    }

    public function canalAdd($id, Request $request){
        $canal_id = $request->input('canal');
        $canal = Canal::find($canal_id);
        $paquete = Paquete::find($id);
        $paquete->canales()->attach($canal);

        return redirect()->route('admin.paquetes.edit', $id)->with('success', 'Canal Agregado al paquete correctamente.');

    }

    public function canalRemove($id, Request $request)
    {
        try {
            // Validar que el canal existe
            $canal = Canal::findOrFail($id);

            // Validar que el paquete existe
            $paquete_id = $request->input('paquete');
            $paquete = Paquete::findOrFail($paquete_id);

            // Eliminar la relación
            $paquete->canales()->detach($canal);

            return redirect()->route('admin.paquetes.edit', $paquete_id)->with('success', 'Canal eliminado del paquete correctamente.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El canal o paquete especificado no existe.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error al eliminar el canal del paquete. Por favor, inténtelo de nuevo.');
        }
    }

    public function cajaPaqueteEdit($id){
        $caja_id = $id;
        $caja = Caja::with('paquetes')->where('id',$caja_id)->first();
        $caja_paquetes = $caja->paquetes;

        $paquetes = Paquete::all();
        $paquetes = $paquetes->diff($caja_paquetes);

        return view('paquetes.cajas.edit')->with('caja', $caja)->with('caja_paquetes', $caja_paquetes)
        ->with('paquetes', $paquetes);
        ;
    }

    public function cajaPaqueteAttach(Request $request){
        $caja_id = $request->input('caja');
        $paquete_id = $request->input('paquete');

        $caja = Caja::find($caja_id);
        $paquete = Paquete::find($paquete_id);
        $caja->paquetes()->attach($paquete);

        return redirect()->route('admin.paquetes.cajas.edit', $caja_id)->with('success', 'Paquete agregado a la caja correctamente.');

    }

    public function cajaPaqueteDettach(Request $request){
        $paquete_id = $request->input('paquete');
        $caja_id = $request->input('caja');

        $paquete = Paquete::find($paquete_id);
        $caja = Caja::with('paquetes')->where('id',$caja_id)->first();

        $caja->paquetes()->detach($paquete);
        return redirect()->route('admin.paquetes.cajas.edit', $caja_id)->with('success', 'Paquete eliminado de la caja');
    }

}
