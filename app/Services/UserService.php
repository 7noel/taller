<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getAll()
    {
        return User::with(['establishment', 'roles'])->get();
    }

    public function getPaginated()
    {
        return User::with(['establishment', 'roles'])
            ->select('users.*')
            ->latest('id')
            ->paginate(15);
    }

    public function findById(int $id): User
    {
        return User::with(['establishment', 'roles'])->findOrFail($id);
    }

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'establishment_id' => $data['establishment_id'] ?? null,
            ]);

            if (!empty($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            return $user;
        });
    }

    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'establishment_id' => $data['establishment_id'] ?? null,
            ]);

            if (!empty($data['password'])) {
                $user->update(['password' => Hash::make($data['password'])]);
            }

            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            return $user->fresh(['establishment', 'roles']);
        });
    }

    public function delete(User $user): ?bool
    {
        return $user->delete();
    }

    public function restore(int $id): User
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return $user;
    }
}