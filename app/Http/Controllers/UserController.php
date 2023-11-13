<?php

namespace App\Http\Controllers;

use App\User;
use App\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("usuarios.usuarios_index", ["usuarios" => User::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("usuarios.usuarios_create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $usuario1 = new User($request->input());
        $existencia = User::where("email", "=", $usuario1->email)->first();
        if ($existencia) {
            return redirect()
                ->route("usuarios.index")
                ->with([
                    "mensaje" => "Usuario existente, por favor elija otro nombre de usuario",
                    "tipo" => "danger"
                ]);
        } else {
            $usuario = new User($request->input());
            $usuario->passwordApp = ($usuario->password);
            $usuario->password = Hash::make($usuario->password);
            $usuario->saveOrFail();
            return redirect()->route("usuarios.index")->with("mensaje", "Usuario guardado");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $user->password = "";
        return view("usuarios.usuarios_edit", [
            "usuario" => $user,
        ]);
    }
    public function info(Request $request)
    {
        $vendedor = request("usuario");
        $vendedor = User::where('id', '=', "{$vendedor}")->first();
        $clientes =  Cliente::where('vendedor', '=', "{$vendedor->name}")->first();

        // echo "EL user: $vendedor";
        return view("usuarios.usuarios_clients", ["usuario" => $vendedor, "clientes" => Cliente::All()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $usuario1 = new User($request->input());
        $user->fill($request->input());
        $user->passwordApp = $user->password;
        $user->password = Hash::make($user->password);
        $user->saveOrFail();
        return redirect()->route("usuarios.index")->with("mensaje", "Usuario actualizado");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route("usuarios.index")->with("mensaje", "Usuario eliminado");
    }
}
