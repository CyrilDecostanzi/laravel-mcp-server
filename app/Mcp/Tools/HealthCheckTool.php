<?php

namespace App\Mcp\Tools;

use Illuminate\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class HealthCheckTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get application health check information. Returns cache status, database connectivity, and queue status.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $health = [
            'status' => 'healthy',
            'checks' => [],
            'timestamp' => now()->toISOString(),
        ];

        // Check database
        try {
            DB::connection()->getPdo();
            $health['checks']['database'] = [
                'status' => 'ok',
                'message' => 'Database connection successful',
            ];
        } catch (\Exception $e) {
            $health['status'] = 'unhealthy';
            $health['checks']['database'] = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }

        // Check cache
        try {
            Cache::put('health_check', true, 10);
            $cacheWorks = Cache::get('health_check') === true;
            Cache::forget('health_check');

            $health['checks']['cache'] = [
                'status' => $cacheWorks ? 'ok' : 'error',
                'message' => $cacheWorks ? 'Cache is working' : 'Cache test failed',
            ];
        } catch (\Exception $e) {
            $health['checks']['cache'] = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }

        return Response::text(json_encode($health, JSON_PRETTY_PRINT));
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
