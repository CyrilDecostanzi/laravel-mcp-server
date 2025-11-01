<?php

namespace App\Mcp\Tools;

use App\Services\User\UserService;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class CreateUserTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Create a new user account with validation. Returns the created user details.
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
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|max:255',
        ]);

        $result = $this->userService->createUser($validated);

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
            'name' => $schema->string()
                ->description('The user\'s full name (2-255 characters)')
                ->required(),
            'email' => $schema->string()
                ->description('The user\'s email address')
                ->required(),
            'password' => $schema->string()
                ->description('The user\'s password (will be hashed, 8-255 characters)')
                ->required(),
        ];
    }
}
