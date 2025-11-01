<?php

namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;

class AppSettingsResource extends Resource
{
    /**
     * The resource's URI.
     */
    protected string $uri = 'config://app/settings';

    /**
     * The resource's MIME type.
     */
    protected string $mimeType = 'application/json';

    /**
     * The resource's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get application configuration as a resource. Returns key application settings.
    MARKDOWN;

    /**
     * Handle the resource request.
     */
    public function handle(Request $request): Response
    {
        $data = [
            'name' => config('app.name'),
            'environment' => app()->environment(),
            'debug' => config('app.debug'),
            'url' => config('app.url'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'available_locales' => config('app.available_locales', ['en']),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'mail_driver' => config('mail.default'),
        ];

        return Response::text(json_encode($data, JSON_PRETTY_PRINT));
    }
}
