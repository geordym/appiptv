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
        $cajas_registro_modified = [];
        foreach($cajas_registro as $caja_registro){

            $mac = $caja_registro->mac;
            $caja = Caja::where('mac', $mac)->first();
            if($caja){
                $caja_registro->isOnSystem = true;
                $caja_registro->nombre = $caja->nombre;
                $cajas_registro_modified[] = $caja_registro;
            } else {
                $cajas_registro_modified[] = $caja_registro;
            }

        }


        return view('cajas.registro')->with('cajas_registro', $cajas_registro_modified);
    }

    public function index(Request $request)
    {
        // Obtén el parámetro 'name' de la solicitud
        $name = $request->input('name');

        // Conteos de cajas activadas y desactivadas
        $conteo_cajas_desactivadas = Caja::where('estado', 'desactivado')->count();
        $conteo_cajas_activadas = Caja::where('estado', 'activado')->count();

        // Si hay un nombre en la solicitud, buscar cajas con un LIKE
        if ($name) {
            $cajas = Caja::where('nombre', 'LIKE', '%' . $name . '%')->get();
        } else {
            // Si no hay nombre, traer todas las cajas
            $cajas = Caja::all();
        }

        // Retornar la vista con los datos
        return view('cajas.index')
            ->with('cajas', $cajas)
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
        try {
            // Validar que el ID de la caja sea numérico y mayor que cero
            if (!is_numeric($id) || $id <= 0) {
                throw new \InvalidArgumentException('El ID de la caja no es válido.');
            }

            // Obtener la caja por su ID
            $caja = Caja::find($id);

            // Verificar si la caja fue encontrada
            if (!$caja) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException('No se encontró la caja especificada.');
            }

            // Devolver la vista con la caja encontrada
            return view('cajas.edit')->with('caja', $caja);
        } catch (\InvalidArgumentException $e) {
            // Manejar errores de validación del ID de la caja
            return redirect()->route('admin.cajas.index')->with('error', $e->getMessage());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Manejar errores cuando no se encuentra la caja
            return redirect()->route('admin.cajas.index')->with('error', $e->getMessage());
        } catch (\Exception $e) {
            // Manejar cualquier otro tipo de error
            return redirect()->route('admin.cajas.index')->with('error', 'Ocurrió un error al cargar la edición de la caja. Por favor, inténtelo de nuevo.');
        }
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
        try {
            // Validar que el ID de la caja sea numérico y mayor que cero
            if (!is_numeric($id) || $id <= 0) {
                throw new \InvalidArgumentException('El ID de la caja no es válido.');
            }

            // Obtener la caja por su ID
            $caja = Caja::findOrFail($id);

            // Eliminar la caja
            $caja->delete();

            // Redireccionar con un mensaje de éxito
            return redirect()->route('cajas.index')->with('success', 'Caja eliminada correctamente.');
        } catch (\InvalidArgumentException $e) {
            // Manejar errores de validación del ID de la caja
            return redirect()->route('cajas.index')->with('error', $e->getMessage());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Manejar errores cuando no se encuentra la caja
            return redirect()->route('cajas.index')->with('error', 'La caja especificada no existe.');
        } catch (\Exception $e) {
            // Manejar cualquier otro tipo de error
            return redirect()->route('cajas.index')->with('error', 'Ocurrió un error al eliminar la caja. Por favor, inténtelo de nuevo.');
        }
    }

}
