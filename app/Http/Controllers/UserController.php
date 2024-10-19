<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function index(){
        return view('home');
    }


    public function profile(){
        $user = auth()->user();
        return view('profile', compact('user'));
    }

    public function users(){
        $users = User::all();
        return view('users')->with('users', $users);
    }

    public function create(Request $request){
        try {
            // Validar los campos
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255|unique:users,name', // Verifica que el campo nombre sea único
                'email' => 'required|string|email|max:255|unique:users,email', // Verifica que el email sea único
                'role' => 'required|string|max:255', // Verifica que el role es requerido
            ]);

            // Obtener los valores validados
            $name = $request->input('nombre');
            $email = $request->input('email');
            $role = $request->input('role');

            // Crear el usuario
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($email), // Encripta el email como contraseña por ahora
                'role' => $role,
            ]);

            // Redirigir con mensaje de éxito
            return redirect('/admin/users')->with('success', 'Usuario creado correctamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si ocurre un error de validación, redirigir de vuelta con los errores
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            // En caso de otro tipo de error, redirigir con mensaje de error
            return redirect('/admin/users')->with('error', 'Ocurrió un error al crear el usuario: ' . $e->getMessage());
        }
    }


    public function logout(){
        Auth::logout(); // Cierra la sesión del usuario
        return redirect('/login'); // Redirige al usuario a la página de inicio de sesión o a la página que desees después del cierre de sesión.
    }

    public function destroy(Request $request)
    {
        try {
            $id = $request->input('user_id');
            $user = User::findOrFail($id);

            if($user->role === "SUPER_ADMINISTRATOR"){
                return redirect('/admin/users')->with('error', 'No se puede eliminar este super administrador.');
            }

            if ($user->role === 'ADMINISTRATOR') {
                $adminCount = User::where('role', 'ADMINISTRATOR')->count();

                if ($adminCount <= 1) {
                    return redirect('/admin/users')->with('error', 'No se puede eliminar el único administrador.');
                }
            }

            $user->delete();

            return redirect('/admin/users')->with('success', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            // En caso de error, redirigir con un mensaje de error
            return redirect('/admin/users')->with('error', 'Ocurrió un error al eliminar el usuario: ' . $e->getMessage());
        }
    }



}
