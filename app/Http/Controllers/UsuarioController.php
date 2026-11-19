<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = $request->string('buscar')->toString();

        $usuarios = User::query()
            ->when($buscar, fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$buscar}%")
                ->orWhere('email', 'like', "%{$buscar}%")))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('usuarios.index', compact('usuarios', 'buscar'));
    }

    public function create(): View
    {
        return view('usuarios.create');
    }

    public function store(UsuarioRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        $usuario = new User();
        $usuario->forceFill([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'rol' => $datos['rol'],
            'password' => Hash::make($datos['password']),
        ])->save();

        return redirect()->route('usuarios.index')->with('ok', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario): View
    {
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(UsuarioRequest $request, User $usuario): RedirectResponse
    {
        $datos = $request->validated();

        if ($usuario->is($request->user()) && $datos['rol'] !== 'admin') {
            return back()->withInput()
                ->withErrors(['rol' => 'No puedes quitarte el rol de administrador a ti mismo.']);
        }

        $usuario->forceFill([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'rol' => $datos['rol'],
        ]);

        if (!empty($datos['password'])) {
            $usuario->password = Hash::make($datos['password']);
        }

        $usuario->save();

        return redirect()->route('usuarios.index')->with('ok', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, User $usuario): RedirectResponse
    {
        if ($usuario->is($request->user())) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('ok', 'Usuario eliminado.');
    }
}