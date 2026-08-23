<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Establishment;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        Gate::authorize('viewAny', User::class);

        return view('users.index');
    }

    public function create()
    {
        Gate::authorize('create', User::class);
        $roles = Role::all()->pluck('name');
        $establishments = Establishment::all()->pluck('name', 'id');

        return view('users.create', compact('roles', 'establishments'));
    }

    public function store(UserRequest $request)
    {
        Gate::authorize('create', User::class);
        $this->userService->create($request->validated());

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function show(User $user)
    {
        Gate::authorize('view', $user);
        $user->load(['establishment', 'roles']);

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        Gate::authorize('update', $user);
        $user->load('roles');
        $roles = Role::all()->pluck('name');
        $establishments = Establishment::all()->pluck('name', 'id');

        return view('users.edit', compact('user', 'roles', 'establishments'));
    }

    public function update(UserRequest $request, User $user)
    {
        Gate::authorize('update', $user);
        $this->userService->update($user, $request->validated());

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);
        $this->userService->delete($user);

        return redirect()->route('users.index')->with('success', 'Usuario eliminado.');
    }

    public function fetchData(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', User::class);

        $users = User::query()
            ->with(['establishment', 'roles'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = $request->query('q');
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('id'), function ($query) use ($request) {
                $query->whereKey($request->query('id'));
            })
            ->orderBy('name')
            ->limit($request->integer('limit', 100))
            ->get();

        return response()->json($users->map(function (User $user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'establishment' => $user->establishment?->name ?? 'Sin asignar',
                'roles' => $user->roles->pluck('name')->implode(', '),
            ];
        }));
    }
}