<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserService
{
    /**
     * Get comprehensive user statistics.
     */
    public function getUserStats(): array
    {
        $totalUsers = User::count();
        $usersWithOrders = User::has('orders')->count();

        $recentSignups = User::whereBetween('created_at', [
            now()->subDays(30),
            now()
        ])->count();

        $oldestUser = User::orderBy('created_at', 'asc')->first();
        $newestUser = User::orderBy('created_at', 'desc')->first();

        $usersByMonth = User::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get()
            ->pluck('count', 'month');

        return [
            'total_users' => $totalUsers,
            'users_with_orders' => $usersWithOrders,
            'users_without_orders' => $totalUsers - $usersWithOrders,
            'recent_signups_30_days' => $recentSignups,
            'oldest_user' => $oldestUser ? [
                'name' => $oldestUser->name,
                'email' => $oldestUser->email,
                'created_at' => $oldestUser->created_at->toISOString(),
            ] : null,
            'newest_user' => $newestUser ? [
                'name' => $newestUser->name,
                'email' => $newestUser->email,
                'created_at' => $newestUser->created_at->toISOString(),
            ] : null,
            'signups_by_month' => $usersByMonth,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Search for users by name or email.
     */
    public function searchUsers(string $query, int $limit = 10): array
    {
        $users = User::where('name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->limit($limit)
            ->get();

        return [
            'query' => $query,
            'total_results' => $users->count(),
            'limit' => $limit,
            'users' => $users->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->toISOString(),
                'updated_at' => $user->updated_at->toISOString(),
            ])->toArray(),
        ];
    }

    /**
     * Create a new user.
     */
    public function createUser(array $data): array
    {
        // Check if user already exists
        if (User::where('email', $data['email'])->exists()) {
            return [
                'success' => false,
                'error' => "User with email {$data['email']} already exists",
            ];
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        return [
            'success' => true,
            'message' => 'User created successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at->toISOString(),
            ],
        ];
    }
}
