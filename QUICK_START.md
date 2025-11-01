# Laravel MCP Server - Quick Start Guide

Get your Laravel MCP server up and running in 5 minutes!

## Prerequisites

- Docker installed and running
- Laravel Sail already set up (already done in this project)

## Step 1: Start the Application (30 seconds)

```bash
./vendor/bin/sail up -d
```

Wait for containers to start. You should see:
```
✓ Container laravel-mcp-server-mysql-1
✓ Container laravel-mcp-server-laravel.test-1
```

## Step 2: Verify MCP Tools (10 seconds)

```bash
./vendor/bin/sail artisan mcp:list
```

You should see **6 tools** and **2 resources** listed.

## Step 3: Test the Tools (30 seconds)

```bash
./vendor/bin/sail php test_mcp_tools.php
```

This will test all 6 MCP tools and show you the output. All tests should pass!

## Step 4: Connect to Claude Desktop (2 minutes)

### Find Your Project Path
```bash
pwd
# Copy this path!
```

### Edit Claude Desktop Config

**Mac**: `~/Library/Application Support/Claude/claude_desktop_config.json`
**Windows**: `%APPDATA%\Claude\claude_desktop_config.json`

Add this (replace `/path/to/project` with your actual path):

```json
{
  "mcpServers": {
    "laravel-mcp": {
      "command": "php",
      "args": [
        "/absolute/path/to/your/project/artisan",
        "mcp:serve",
        "--transport=stdio"
      ]
    }
  }
}
```

### Restart Claude Desktop

The MCP server will now be available in Claude Desktop!

## Step 5: Try It Out!

Ask Claude:
- "What are the user statistics?"
- "Show me system information"
- "Check the application health"
- "What database tables exist?"

## Available Tools

### 6 MCP Tools:
1. **get_user_stats** - User statistics
2. **search_users** - Search for users
3. **create_user** - Create new user
4. **get_system_info** - System/app info
5. **get_database_info** - Database stats
6. **health_check** - Health monitoring

### 2 MCP Resources:
1. **config://app/settings** - App configuration
2. **system://laravel/info** - Laravel info

## Troubleshooting

### Tools not showing up in Claude?
```bash
# Restart containers
./vendor/bin/sail down
./vendor/bin/sail up -d

# Rediscover tools
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan mcp:discover
```

### Want to add more tools?
1. Edit `app/Services/LaravelMcpService.php`
2. Add a method with `#[McpTool]` attribute
3. Run: `./vendor/bin/sail artisan mcp:discover`

## Example: Adding a New Tool

```php
// In app/Services/LaravelMcpService.php

#[McpTool(name: 'count_users')]
public function countUsers(): array
{
    return [
        'total' => User::count(),
        'timestamp' => now()->toISOString(),
    ];
}
```

Then:
```bash
./vendor/bin/sail artisan mcp:discover
./vendor/bin/sail artisan mcp:list
```

## What's Next?

- Read `MCP_IMPLEMENTATION.md` for detailed documentation
- Check `MCP_POC_SUMMARY.md` for implementation details
- Review `CLAUDE.md` for development workflow

## HTTP Transport (Alternative)

If you prefer HTTP over STDIO:

```bash
./vendor/bin/sail artisan mcp:serve --transport=http
```

Access at: `http://localhost:8000/mcp`

---

That's it! You now have a fully functional Laravel MCP server.
