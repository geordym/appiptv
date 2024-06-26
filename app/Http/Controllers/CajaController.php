<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CajaRegistro;
use App\Rules\MacAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CajaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


     public function registroCajas(){
        $cajas_registro = CajaRegistro::orderBy('created_at', 'DESC')->limit(30)->get();
        return view('cajas.registro')->with('cajas_registro', $cajas_registro);
    }

    public function index()
    {
        //

        $conteo_cajas_desactivadas = Caja::where('estado', 'desactivado')->count();
        $conteo_cajas_activadas = Caja::where('estado', 'activado')->count();


        $cajas = Caja::all();
        return view('cajas.index')->with('cajas', $cajas)
        ->with('cajas_activadas', $conteo_cajas_activadas)
        ->with('cajas_desactivadas', $conteo_cajas_desactivadas);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $mac = $request->input('mac');
        return view('cajas.create')->with('mac', $mac);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validación de los datos recibidos
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string',
            'estado' => 'required|in:activado,desactivado',
        ]);

        $request->validate([
            'mac' => ['required', new MacAddress, Rule::unique('cajas', 'mac')],
        ]);

        // Comprobar si la validación falla
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Crear una nueva instancia de Caja
        $caja = new Caja();
        $caja->mac = $request->mac;
        $caja->nombre = $request->nombre;
        $caja->estado = $request->estado;
        $caja->save();

        // Redireccionar con un mensaje
        return redirect()->route('cajas.index')->with('success', 'Caja creada correctamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $caja = Caja::find($id);
        return view('cajas.edit')->with('caja', $caja);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        // Validación de los datos recibidos
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string',
            'estado' => 'required|in:activado,desactivado',
        ]);

        $request->validate([
            'mac' => ['required', new MacAddress],
        ]);

        // Comprobar si la validación falla
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Encontrar la caja a actualizar
        $caja = Caja::findOrFail($id);

        // Actualizar los valores de la caja
        $caja->mac = $request->mac;
        $caja->nombre = $request->nombre;
        $caja->estado = $request->estado;
        $caja->save();

        // Redireccionar con un mensaje
        return redirect()->route('cajas.index')->with('success', 'Caja actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Encontrar la caja a eliminar
        $caja = Caja::findOrFail($id);

        // Eliminar la caja
        $caja->delete();

        // Redireccionar con un mensaje
        return redirect()->route('cajas.index')->with('success', 'Caja eliminada correctamente.');
    }
}
