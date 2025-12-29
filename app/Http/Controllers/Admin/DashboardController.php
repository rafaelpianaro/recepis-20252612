<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\UserRole;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with statistics.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', User::class);

        // Statistics
        $stats = [
            'total_users' => User::count(),
            'admins' => User::where('role', UserRole::ADMIN)->count(),
            'managers' => User::where('role', UserRole::MANAGER)->count(),
            'users' => User::where('role', UserRole::USER)->count(),
            'users_last_7_days' => User::where('created_at', '>=', now()->subDays(7))->count(),
            'users_last_30_days' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        // Recent users (last 5)
        $recent_users = User::query()
            ->select(['id', 'name', 'email', 'role', 'created_at'])
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->value,
                    'role_label' => $user->role->label(),
                    'created_at' => $user->created_at->toISOString(),
                    'created_at_human' => $user->created_at->diffForHumans(),
                ];
            });

        // Users by role (for chart)
        $users_by_role = [
            ['role' => 'Administrator', 'count' => $stats['admins']],
            ['role' => 'Manager', 'count' => $stats['managers']],
            ['role' => 'User', 'count' => $stats['users']],
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recent_users' => $recent_users,
            'users_by_role' => $users_by_role,
        ]);
    }
}
