<?php

namespace App\Mcp\Tools;

use App\Services\User\UserService;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class SearchUsersTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Search for users by name or email. Returns a list of matching users with their details.
    MARKDOWN;

    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2|max:100',
            'limit' => 'integer|min:1|max:50',
        ]);

        $result = $this->userService->searchUsers(
            $validated['query'],
            $validated['limit'] ?? 10
        );

        return Response::text(json_encode($result, JSON_PRETTY_PRINT));
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()
                ->description('The search query to match against user names and emails (2-100 characters)')
                ->required(),
            'limit' => $schema->integer()
                ->description('Maximum number of results to return (1-50, default: 10)')
                ->default(10),
        ];
    }
}
