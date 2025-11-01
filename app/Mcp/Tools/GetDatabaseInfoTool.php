<?php

namespace App\Mcp\Tools;

use Illuminate\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetDatabaseInfoTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get detailed database statistics and table information. Returns list of tables with row counts and sizes.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $connection = DB::connection();
        $tables = DB::select('SHOW TABLES');
        $databaseName = $connection->getDatabaseName();

        $tableInfo = [];
        foreach ($tables as $table) {
            $tableName = $table->{"Tables_in_{$databaseName}"};
            $rowCount = DB::table($tableName)->count();

            $tableInfo[] = [
                'name' => $tableName,
                'rows' => $rowCount,
            ];
        }

        $data = [
            'database' => $databaseName,
            'driver' => $connection->getDriverName(),
            'total_tables' => count($tableInfo),
            'tables' => $tableInfo,
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
