<?php

namespace App\Services\System;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SystemHealthService
{
    /**
     * Perform application health check.
     */
    public function checkHealth(): array
    {
        $health = [
            'status' => 'healthy',
            'checks' => [],
            'timestamp' => now()->toISOString(),
        ];

        // Check database
        $health['checks']['database'] = $this->checkDatabase();
        if ($health['checks']['database']['status'] === 'error') {
            $health['status'] = 'unhealthy';
        }

        // Check cache
        $health['checks']['cache'] = $this->checkCache();
        if ($health['checks']['cache']['status'] === 'error') {
            $health['status'] = 'degraded';
        }

        return $health;
    }

    /**
     * Get system information.
     */
    public function getSystemInfo(): array
    {
        return [
            'application' => [
                'name' => config('app.name'),
                'environment' => config('app.env'),
                'debug' => config('app.debug'),
                'url' => config('app.url'),
                'timezone' => config('app.timezone'),
                'locale' => config('app.locale'),
            ],
            'laravel' => [
                'version' => app()->version(),
                'php_version' => PHP_VERSION,
            ],
            'database' => [
                'connection' => config('database.default'),
                'driver' => config('database.connections.'.config('database.default').'.driver'),
            ],
            'cache' => [
                'driver' => config('cache.default'),
            ],
            'queue' => [
                'driver' => config('queue.default'),
            ],
            'session' => [
                'driver' => config('session.driver'),
                'lifetime' => config('session.lifetime'),
            ],
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get database information.
     */
    public function getDatabaseInfo(): array
    {
        $tables = DB::select('SHOW TABLES');
        $databaseName = config('database.connections.'.config('database.default').'.database');
        $tableKey = "Tables_in_{$databaseName}";

        $tableData = collect($tables)->map(function ($table) use ($tableKey) {
            $tableName = $table->$tableKey;
            $count = DB::table($tableName)->count();

            return [
                'table' => $tableName,
                'rows' => $count,
            ];
        })->sortByDesc('rows');

        return [
            'database' => $databaseName,
            'connection' => config('database.default'),
            'driver' => config('database.connections.'.config('database.default').'.driver'),
            'tables' => $tableData->values()->toArray(),
            'total_tables' => $tableData->count(),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Check database connectivity.
     */
    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();

            return [
                'status' => 'ok',
                'message' => 'Database connection successful',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check cache functionality.
     */
    private function checkCache(): array
    {
        try {
            Cache::put('health_check', true, 10);
            $cacheWorks = Cache::get('health_check') === true;
            Cache::forget('health_check');

            return [
                'status' => $cacheWorks ? 'ok' : 'error',
                'message' => $cacheWorks ? 'Cache is working' : 'Cache test failed',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}
