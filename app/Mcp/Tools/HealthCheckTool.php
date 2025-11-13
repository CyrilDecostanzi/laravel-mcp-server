<?php

namespace App\Mcp\Tools;

use App\Services\System\SystemHealthService;
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

    public function __construct(
        private readonly SystemHealthService $systemHealthService
    ) {}

    /**
     * Handle the tool request.
     *
     * @throws \JsonException
     */
    public function handle(Request $request): Response
    {
        $result = $this->systemHealthService->checkHealth();

        return Response::text(json_encode($result, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    }
}
