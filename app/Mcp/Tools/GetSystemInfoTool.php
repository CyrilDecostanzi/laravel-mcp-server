<?php

namespace App\Mcp\Tools;

use App\Services\System\SystemHealthService;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetSystemInfoTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get comprehensive application and system information. Returns Laravel version, PHP version, environment, database info, and more.
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
        $result = $this->systemHealthService->getSystemInfo();

        return Response::text(json_encode($result, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            // No input parameters required
        ];
    }
}
