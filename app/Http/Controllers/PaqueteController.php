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
        // Validar los datos de entrada
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        try {
            // Crear un nuevo paquete
            $paquete = new Paquete();
            $paquete->nombre = $validated['nombre'];
            $paquete->save();

            return redirect()->route('admin.paquetes.index')->with('success', 'Paquete creado correctamente.');
        } catch (\Exception $e) {
            // Manejar errores y redirigir con mensaje de error
            return redirect()->route('admin.paquetes.index')->with('error', 'Ocurrió un error al crear el paquete. Por favor, inténtelo de nuevo.');
        }
    }

    public function destroy($id)
    {
        try {
            // Validar que el ID del paquete sea numérico y mayor que cero
            if (!is_numeric($id) || $id <= 0) {
                throw new \InvalidArgumentException('El ID del paquete no es válido.');
            }

            // Encontrar el paquete por su ID
            $paquete = Paquete::findOrFail($id);

            // Eliminar todos los registros en paquetes_canales asociados a este paquete
            $paquete->canales()->detach();

            // Eliminar el paquete
            $paquete->delete();

            // Redireccionar con un mensaje de éxito
            return redirect()->route('admin.paquetes.index')->with('success', 'Paquete eliminado correctamente.');
        } catch (\InvalidArgumentException $e) {
            // Manejar errores de validación del ID del paquete
            return redirect()->route('admin.paquetes.index')->with('error', $e->getMessage());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Manejar errores cuando no se encuentra el paquete
            return redirect()->route('admin.paquetes.index')->with('error', 'El paquete especificado no existe.');
        } catch (\Exception $e) {
            // Manejar cualquier otro tipo de error
            return redirect()->route('admin.paquetes.index')->with('error', 'Ocurrió un error al eliminar el paquete. Por favor, inténtelo de nuevo.');
        }
    }




    public function edit($id)
    {
        try {
            // Validar que el ID del paquete sea numérico y mayor que cero
            if (!is_numeric($id) || $id <= 0) {
                throw new \InvalidArgumentException('El ID del paquete no es válido.');
            }

            // Obtener todos los canales
            $canales = Canal::all();

            // Obtener el paquete con sus canales asociados
            $paquete = Paquete::with('canales')->findOrFail($id);
            $paquete_canales = $paquete->canales;

            // Filtrar los canales que no están en el paquete
            $canales_filtrados = $canales->diff($paquete_canales);

            // Devolver la vista con los datos necesarios
            return view('paquetes.edit', [
                'canales' => $canales_filtrados,
                'paquete' => $paquete,
                'paquete_canales' => $paquete_canales
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.paquetes.index')->with('error', 'El paquete especificado no existe.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('admin.paquetes.index')->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('admin.paquetes.index')->with('error', 'Ocurrió un error al cargar la edición del paquete. Por favor, inténtelo de nuevo.');
        }
    }


    public function canalAdd($id, Request $request)
    {
        // Validar los datos de entrada
        $validated = $request->validate([
            'canal' => 'required|exists:canales,id',
        ]);

        try {
            // Obtener el canal y el paquete
            $canal = Canal::findOrFail($validated['canal']);
            $paquete = Paquete::findOrFail($id);

            // Vincular el canal al paquete
            $paquete->canales()->attach($canal->id);

            // Redirigir con mensaje de éxito
            return redirect()->route('admin.paquetes.edit', $id)->with('success', 'Canal agregado al paquete correctamente.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El canal o paquete especificado no existe.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error al agregar el canal al paquete. Por favor, inténtelo de nuevo.');
        }
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
            $paquete->canales()->detach($canal->id);

            return redirect()->route('admin.paquetes.edit', $paquete_id)->with('success', 'Canal eliminado del paquete correctamente.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'El canal o paquete especificado no existe.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error al eliminar el canal del paquete. Por favor, inténtelo de nuevo.');
        }
    }


    public function cajaPaqueteEdit($id)
    {
        try {
            // Obtener la caja con sus paquetes asociados
            $caja = Caja::with('paquetes')->findOrFail($id);

            // Obtener los paquetes que no están en la caja
            $paquetes = Paquete::all()->diff($caja->paquetes);

            // Devolver la vista con los datos necesarios
            return view('paquetes.cajas.edit', [
                'caja' => $caja,
                'caja_paquetes' => $caja->paquetes,
                'paquetes' => $paquetes
            ]);
        } catch (\Exception $e) {
            // Manejar errores y redirigir con mensaje de error
            return redirect()->route('admin.paquetes.cajas.index')->with('error', 'Ocurrió un error al cargar la edición de la caja: ' . $e->getMessage());
        }
    }


    public function cajaPaqueteAttach(Request $request)
    {
        // Validar los datos de entrada
        $validated = $request->validate([
            'caja' => 'required|exists:cajas,id',
            'paquete' => 'required|exists:paquetes,id',
        ]);

        try {
            // Obtener la caja y el paquete
            $caja = Caja::findOrFail($validated['caja']);
            $paquete = Paquete::findOrFail($validated['paquete']);

            // Vincular el paquete a la caja
            $caja->paquetes()->attach($paquete->id);

            // Redirigir con mensaje de éxito
            return redirect()->route('admin.paquetes.cajas.edit', $caja->id)->with('success', 'Paquete agregado a la caja correctamente.');
        } catch (\Exception $e) {
            // Manejar errores y redirigir con mensaje de error
            return redirect()->route('admin.paquetes.cajas.edit', $validated['caja'])->with('error', 'Ocurrió un error al agregar el paquete a la caja: ' . $e->getMessage());
        }
    }


    public function cajaPaqueteDettach(Request $request)
    {
        // Validar los datos de entrada
        $validated = $request->validate([
            'paquete' => 'required|exists:paquetes,id',
            'caja' => 'required|exists:cajas,id',
        ]);

        try {
            // Obtener el paquete y la caja con sus relaciones
            $paquete = Paquete::findOrFail($validated['paquete']);
            $caja = Caja::with('paquetes')->findOrFail($validated['caja']);

            // Desvincular el paquete de la caja
            $caja->paquetes()->detach($paquete->id);

            // Redirigir con mensaje de éxito
            return redirect()->route('admin.paquetes.cajas.edit', $caja->id)->with('success', 'Paquete eliminado de la caja');
        } catch (\Exception $e) {
            // Manejar errores y redirigir con mensaje de error
            return redirect()->route('admin.paquetes.cajas.edit', $validated['caja'])->with('error', 'Ocurrió un error al eliminar el paquete de la caja: ' . $e->getMessage());
        }
    }


}
