<?php

namespace App\Http\Controllers;

use App\Domain\Auth\Models\User;
use App\Domain\Auth\Repositories\UserRepository;
use App\Domain\Shared\DTOs\ListFilterData;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class UserController extends Controller
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $filter = new ListFilterData(
            search: $request->input('search'),
            perPage: $request->input('per_page', 15)
        );

        $query = User::query();

        // Aplica filtro de busca unificado
        if ($filter->hasSearch()) {
            $query = $this->applySearch($query, $filter->search);
        }

        $users = $query->paginate($filter->perPage);

        return view('users.index', compact('users', 'filter'));
    }

    /**
     * Aplica filtro de busca nos campos exibidos
     */
    private function applySearch(Builder $query, string $search): Builder
    {
        $searchLower = mb_strtolower(trim($search));

        return $query->where(function (Builder $q) use ($searchLower) {
            $q->orWhereRaw('LOWER(name) LIKE ?', ['%' . $searchLower . '%'])
              ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $searchLower . '%']);
        });
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
