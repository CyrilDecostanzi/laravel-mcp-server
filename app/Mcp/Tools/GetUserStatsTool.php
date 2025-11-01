<?php

namespace App\Mcp\Tools;

use App\Models\User;
use Illuminate\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetUserStatsTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get comprehensive user statistics from the database. Returns total users, recently created users, and user distribution.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $totalUsers = User::count();
        $recentUsers = User::where('created_at', '>=', now()->subDays(7))->count();
        $usersToday = User::whereDate('created_at', today())->count();
        $oldestUser = User::orderBy('created_at', 'asc')->first();
        $newestUser = User::orderBy('created_at', 'desc')->first();

        $data = [
            'total_users' => $totalUsers,
            'users_last_7_days' => $recentUsers,
            'users_today' => $usersToday,
            'oldest_user' => $oldestUser ? [
                'id' => $oldestUser->id,
                'name' => $oldestUser->name,
                'email' => $oldestUser->email,
                'created_at' => $oldestUser->created_at->toISOString(),
            ] : null,
            'newest_user' => $newestUser ? [
                'id' => $newestUser->id,
                'name' => $newestUser->name,
                'email' => $newestUser->email,
                'created_at' => $newestUser->created_at->toISOString(),
            ] : null,
            'database_connection' => DB::connection()->getDatabaseName(),
            'timestamp' => now()->toISOString(),
        ];

        return Response::text(json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            // No input parameters required
        ];
    }
}
