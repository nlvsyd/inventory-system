<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(): Response
    {
        $users = User::select('id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at')
            ->with('roles')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    'role' => $user->roles->first()?->name ?? 'No Role',
                ];
            });

        return Inertia::render('Users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): Response
    {
        $roles = Role::whereIn('name', ['System Admin', 'Suppliers', 'Customers'])
            ->orderBy('name')
            ->get()
            ->pluck('name');

        return Inertia::render('Users/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $role = $validated['role'];
        unset($validated['role']);

        $user = User::create($validated);
        $user->assignRole($role);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): Response
    {
        $roles = Role::whereIn('name', ['System Admin', 'Suppliers', 'Customers'])
            ->orderBy('name')
            ->get()
            ->pluck('name');

        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()?->name ?? '',
            ],
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();
        $role = $validated['role'];
        unset($validated['role']);

        // Ensure password is not included if it's null or empty
        if (isset($validated['password']) && (empty($validated['password']) || $validated['password'] === null)) {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->syncRoles([$role]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
