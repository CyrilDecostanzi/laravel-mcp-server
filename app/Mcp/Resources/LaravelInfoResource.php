<?php

namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;

class LaravelInfoResource extends Resource
{
    /**
     * The resource's URI.
     */
    protected string $uri = 'system://laravel/info';

    /**
     * The resource's MIME type.
     */
    protected string $mimeType = 'application/json';

    /**
     * The resource's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get Laravel version and environment information as a resource.
    MARKDOWN;

    /**
     * Handle the resource request.
     */
    public function handle(Request $request): Response
    {
        $data = [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'environment' => app()->environment(),
            'running_in_console' => app()->runningInConsole(),
            'locale' => app()->getLocale(),
            'debug_mode' => config('app.debug'),
        ];

        return Response::text(json_encode($data, JSON_PRETTY_PRINT));
    }
}
