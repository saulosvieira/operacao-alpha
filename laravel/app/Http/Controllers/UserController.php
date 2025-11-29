<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $filters = request()->only(['name', 'email']);
        $users = $this->service->paginateWithFilters($filters, 15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,consultor',
        ]);
        $validated['password'] = \Hash::make($validated['password']);
        $this->service->create($validated);

        return redirect()->route('usuarios.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function show($id)
    {
        $user = $this->service->find($id);

        return view('users.show', compact('user'));
    }

    public function showModal($id)
    {
        $user = $this->service->find($id);

        return view('users.partials.details', compact('user'));
    }

    public function edit($id)
    {
        $user = $this->service->find($id);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,consultor',
        ]);
        if ($validated['password']) {
            $validated['password'] = \Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        $this->service->update($id, $validated);

        return redirect()->route('usuarios.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $this->service->delete($id);

        return redirect()->route('usuarios.index')->with('success', 'Usuário excluído com sucesso!');
    }
}
