<?php

namespace App\Mcp\Tools;

use App\Services\System\SystemHealthService;
use JsonException;
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

    public function __construct(
        private readonly SystemHealthService $systemHealthService
    ) {}

    /**
     * Handle the tool request.
     *
     * @throws JsonException
     */
    public function handle(Request $request): Response
    {
        $result = $this->systemHealthService->getDatabaseInfo();

        return Response::text(json_encode($result, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    }
}
