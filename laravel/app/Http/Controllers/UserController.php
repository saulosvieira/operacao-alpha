<?php

namespace App\Http\Controllers;

use App\Domain\Auth\Models\User;
use App\Domain\Auth\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $filters = request()->only(['name', 'email']);
        
        $query = User::query();
        
        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }
        
        if (!empty($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }
        
        $users = $query->paginate(15);

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
        
        $this->repository->create($validated);

        return redirect()->route('admin.users.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        return view('users.show', compact('user'));
    }

    public function showModal($id)
    {
        $user = User::findOrFail($id);

        return view('users.partials.details', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

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
        
        if (empty($validated['password'])) {
            unset($validated['password']);
        }
        
        $this->repository->update($id, $validated);

        return redirect()->route('admin.users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $this->repository->delete($id);

        return redirect()->route('admin.users.index')->with('success', 'Usuário excluído com sucesso!');
    }
}
