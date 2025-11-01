# Laravel MCP Server - Implementation Guide

## Overview

This Laravel application now has a fully functional MCP (Model Context Protocol) server implementation using the official `php-mcp/laravel` package. The server exposes 6 tools and 2 resources that Claude or other AI assistants can use to interact with your Laravel application.

## What's Been Implemented

### MCP Tools (6 total)

1. **get_user_stats**
   - Get comprehensive user statistics from the database
   - Returns total users, recent signups, oldest/newest users

2. **search_users**
   - Search for users by name or email
   - Parameters: `query` (string, min 2 chars), `limit` (int, default 10)
   - Returns matching users with their details

3. **create_user**
   - Create a new user account with validation
   - Parameters: `name`, `email`, `password` (min 8 chars)
   - Validates email uniqueness

4. **get_system_info**
   - Get comprehensive application and system information
   - Returns Laravel version, PHP version, environment details, database config

5. **get_database_info**
   - Get detailed database statistics and table information
   - Returns all tables with row counts

6. **health_check**
   - Application health check
   - Checks database connectivity, cache functionality
   - Returns status for each component

### MCP Resources (2 total)

1. **config://app/settings**
   - Application configuration settings
   - Returns app name, environment, drivers, etc.

2. **system://laravel/info**
   - Laravel version and environment information
   - Quick access to runtime information

## Configuration Files

### config/mcp.php

Main configuration file for the MCP server:
- **Discovery paths**: `app/Services` and `app/Mcp`
- **Transports**: HTTP (on `/mcp` endpoint) and STDIO
- **Capabilities**: All tools, resources, and prompts enabled
- **Cache**: Uses default Laravel cache

### routes/mcp.php

MCP routes file for manual registration (currently using auto-discovery via attributes).

### app/Services/LaravelMcpService.php

Service class containing all MCP tools and resources, using PHP 8 attributes for automatic discovery.

## How to Use

### 1. Start the MCP Server

#### Using STDIO Transport (for Claude Desktop, Cursor, etc.)

```bash
php artisan mcp:serve --transport=stdio
```

**Configuration for Claude Desktop** (add to `claude_desktop_config.json`):

```json
{
  "mcpServers": {
    "laravel-mcp": {
      "command": "php",
      "args": [
        "/absolute/path/to/your/laravel/project/artisan",
        "mcp:serve",
        "--transport=stdio"
      ]
    }
  }
}
```

#### Using HTTP Transport (for web-based integrations)

```bash
# Start the dedicated HTTP server
php artisan mcp:serve --transport=http

# Or use the integrated transport (auto-available at /mcp endpoint when app runs)
./vendor/bin/sail up -d
# Server available at http://localhost:8000/mcp
```

**Configuration for HTTP clients**:

```json
{
  "mcpServers": {
    "laravel-mcp": {
      "url": "http://localhost:8000/mcp"
    }
  }
}
```

### 2. Discover MCP Elements

After making changes to your MCP tools/resources:

```bash
./vendor/bin/sail artisan mcp:discover
```

### 3. List Available Tools

To see all registered MCP elements:

```bash
./vendor/bin/sail artisan mcp:list

# List specific type
./vendor/bin/sail artisan mcp:list tools
./vendor/bin/sail artisan mcp:list resources

# JSON output
./vendor/bin/sail artisan mcp:list --json
```

## Example Usage

Once connected to Claude Desktop or another MCP client, you can ask Claude to:

1. **"Get user statistics from the database"**
   - Claude will call the `get_user_stats` tool

2. **"Search for users with email containing 'john'"**
   - Claude will call `search_users` with query="john"

3. **"Create a new user named Alice with email alice@example.com"**
   - Claude will call `create_user` with the provided details

4. **"What's the application environment and Laravel version?"**
   - Claude will call `get_system_info`

5. **"Show me all database tables and their row counts"**
   - Claude will call `get_database_info`

6. **"Check the application health"**
   - Claude will call `health_check`

## Testing the MCP Server

### Quick Test with Tinker

You can test individual tools using Laravel Tinker:

```bash
./vendor/bin/sail artisan tinker

# Test the service
$service = new App\Services\LaravelMcpService();
$service->getUserStats();
$service->getSystemInfo();
$service->healthCheck();
```

### Create Test Users

```bash
./vendor/bin/sail artisan tinker

# Create a test user
User::factory()->create(['name' => 'Test User', 'email' => 'test@example.com']);

# Create multiple users
User::factory()->count(10)->create();
```

## Adding New MCP Tools

To add new tools, simply add methods to `LaravelMcpService` with the `#[McpTool]` attribute:

```php
use PhpMcp\Server\Attributes\McpTool;
use PhpMcp\Server\Attributes\Schema;

#[McpTool(name: 'your_tool_name')]
public function yourToolMethod(
    #[Schema(minLength: 1)]
    string $param1,

    #[Schema(minimum: 0)]
    int $param2 = 10
): array {
    // Your logic here
    return ['result' => 'data'];
}
```

Then run discovery:

```bash
./vendor/bin/sail artisan mcp:discover
```

## Adding New Resources

Add methods with the `#[McpResource]` attribute:

```php
use PhpMcp\Server\Attributes\McpResource;

#[McpResource(
    uri: 'your://resource/uri',
    mimeType: 'application/json'
)]
public function yourResource(): array {
    return ['data' => 'value'];
}
```

## Troubleshooting

### Tools Not Discovered

1. Check that your service class is in `app/Services` or `app/Mcp`
2. Run `php artisan config:clear`
3. Run `php artisan mcp:discover`
4. Verify with `php artisan mcp:list`

### Server Not Starting

1. Check if ports are available (8000 for HTTP)
2. Verify Sail containers are running: `./vendor/bin/sail ps`
3. Check logs: `./vendor/bin/sail logs`

### CSRF Token Errors (HTTP Transport)

The HTTP transport uses `api` middleware which doesn't require CSRF tokens by default. If you encounter CSRF issues, check the middleware configuration in `config/mcp.php`.

## Production Deployment

For production, use a process supervisor like Supervisor to keep the MCP server running:

```ini
[program:laravel-mcp]
process_name=%(program_name)s
command=php /var/www/laravel/artisan mcp:serve --transport=http
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/laravel-mcp.log
```

## Security Notes

- The MCP tools have direct database access
- Consider adding authentication/authorization middleware for production
- Validate all inputs (already done with `#[Schema]` attributes)
- Use environment-specific configurations
- Consider rate limiting for HTTP transport

## Next Steps

1. Add authentication to MCP tools if needed
2. Create more specialized tools for your use case
3. Add prompts for common AI interactions
4. Implement resource templates for dynamic content
5. Add monitoring and logging for MCP requests

## Resources

- [php-mcp/laravel Documentation](https://github.com/php-mcp/laravel)
- [MCP Protocol Specification](https://modelcontextprotocol.io)
- [Laravel Documentation](https://laravel.com/docs)
