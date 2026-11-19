<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistroRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegistroController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegistroRequest $request): RedirectResponse
    {
        // El rol nunca viene del formulario: quien se registra es cliente.
        $usuario = new User();
        $usuario->forceFill([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'rol' => 'cliente',
        ])->save();

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('catalogo.index')
            ->with('ok', 'Cuenta creada. Bienvenido a Tienda Los Paisas.');
    }
}