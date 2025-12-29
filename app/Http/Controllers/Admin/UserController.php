<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Requests\Admin\UpdateUserRoleRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->input('role'), function ($query, $role) {
                $query->where('role', $role);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Statistics
        $stats = [
            'total' => User::count(),
            'admins' => User::where('role', UserRole::ADMIN)->count(),
            'managers' => User::where('role', UserRole::MANAGER)->count(),
            'users' => User::where('role', UserRole::USER)->count(),
        ];

        return Inertia::render('Admin/Users', [
            'users' => UserResource::collection($users),
            'stats' => $stats,
            'filters' => [
                'search' => $request->input('search'),
                'role' => $request->input('role'),
            ],
            'roles' => [
                ['value' => UserRole::ADMIN->value, 'label' => UserRole::ADMIN->label()],
                ['value' => UserRole::MANAGER->value, 'label' => UserRole::MANAGER->label()],
                ['value' => UserRole::USER->value, 'label' => UserRole::USER->label()],
            ],
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        Log::info('User created by admin', [
            'admin_id' => $request->user()->id,
            'admin_name' => $request->user()->name,
            'new_user_id' => $user->id,
            'new_user_email' => $user->email,
            'new_user_role' => $user->role->value,
        ]);

        return back()->with('status', 'user-created');
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        // Prevent admins from editing their own account through this method
        if ($request->user()->id === $user->id) {
            return back()->withErrors([
                'error' => 'You cannot edit your own account through this interface. Use Profile Settings instead.',
            ]);
        }

        $validated = $request->validated();

        $changes = [];
        
        if ($user->name !== $validated['name']) {
            $changes['name'] = ['old' => $user->name, 'new' => $validated['name']];
        }
        
        if ($user->email !== $validated['email']) {
            $changes['email'] = ['old' => $user->email, 'new' => $validated['email']];
        }
        
        if ($user->role->value !== $validated['role']) {
            $changes['role'] = ['old' => $user->role->value, 'new' => $validated['role']];
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
            $changes['password'] = 'changed';
        }

        if (!empty($changes)) {
            Log::info('User updated by admin', [
                'admin_id' => $request->user()->id,
                'admin_name' => $request->user()->name,
                'user_id' => $user->id,
                'changes' => $changes,
            ]);
        }

        return back()->with('status', 'user-updated');
    }

    /**
     * Update the specified user's role.
     */
    public function updateRole(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        $this->authorize('changeRole', $user);

        // Prevent users from changing their own role
        if ($request->user()->id === $user->id) {
            return back()->withErrors([
                'role' => 'You cannot change your own role.',
            ]);
        }

        $validated = $request->validated();
        
        $oldRole = $user->role->value;
        $newRole = $validated['role'];

        $user->update([
            'role' => $newRole,
        ]);

        Log::info('User role changed', [
            'admin_id' => $request->user()->id,
            'admin_name' => $request->user()->name,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'old_role' => $oldRole,
            'new_role' => $newRole,
        ]);

        return back()->with('status', 'role-updated');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        // Prevent admins from deleting themselves
        if ($request->user()->id === $user->id) {
            return back()->withErrors([
                'error' => 'You cannot delete your own account.',
            ]);
        }

        $userName = $user->name;
        $userEmail = $user->email;
        $userRole = $user->role->value;

        $user->delete();

        Log::warning('User deleted by admin', [
            'admin_id' => $request->user()->id,
            'admin_name' => $request->user()->name,
            'deleted_user_id' => $user->id,
            'deleted_user_name' => $userName,
            'deleted_user_email' => $userEmail,
            'deleted_user_role' => $userRole,
        ]);

        return back()->with('status', 'user-deleted');
    }
}
