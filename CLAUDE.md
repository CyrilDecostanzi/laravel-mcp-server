# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A production-ready Laravel 12 MCP (Model Context Protocol) server that provides AI assistants with structured access to e-commerce business data. The server implements 15 MCP tools, 2 resources, and demonstrates enterprise-grade architecture patterns for AI-to-database integration.

## Development Environment Setup

### Starting the Application

```bash
# Start all containers
./vendor/bin/sail up -d

# Stop containers
./vendor/bin/sail down

# Rebuild containers (use after Dockerfile changes)
./vendor/bin/sail build --no-cache
./vendor/bin/sail up -d
```

### Database Management

```bash
# Run migrations
./vendor/bin/sail artisan migrate

# Fresh migration with seed data (500 orders, 200 products, 100 users)
./vendor/bin/sail artisan migrate:fresh --seed

# Access MySQL CLI
./vendor/bin/sail mysql

# View database structure
./vendor/bin/sail artisan db:show
```

### Testing and Code Quality

```bash
# Run test suite
./vendor/bin/sail test

# Run specific test file
./vendor/bin/sail artisan test tests/Feature/Auth/AuthenticationTest.php

# Code formatting with Laravel Pint
./vendor/bin/sail pint

# Interactive REPL for testing services
./vendor/bin/sail artisan tinker
```

### MCP Development

```bash
# Start MCP Inspector (interactive web UI for testing MCP tools)
./vendor/bin/sail artisan mcp:inspector laravel
# Then open http://localhost:6274

# Generate new MCP tool
./vendor/bin/sail artisan make:mcp-tool YourToolName

# Start MCP server (for Claude Desktop integration)
./vendor/bin/sail artisan mcp:start laravel
```

### Accessing Services

- **Application**: http://localhost
- **phpMyAdmin**: http://localhost:8080 (user: `sail`, password: `password`)
- **MCP Inspector**: http://localhost:6274
- **MySQL Port**: 3307 (forwarded from container port 3306)

## Architecture

### Three-Layer Architecture

The application follows strict separation of concerns with three distinct layers:

1. **MCP Tools Layer** (`app/Mcp/Tools/`): Thin controllers that handle MCP protocol requests/responses. No business logic.
2. **Service Layer** (`app/Services/`): Contains all business logic, calculations, and data transformations. Injected into tools via dependency injection.
3. **Data Layer** (`app/Models/`): Eloquent models representing database entities and relationships.

### Key Design Patterns

**Dependency Injection**: All services are automatically injected into MCP tools through Laravel's service container. Example:

```php
public function __construct(
    private readonly SalesAnalyticsService $salesAnalyticsService
) {}
```

**Service Organization**: Services are organized by domain:
- `app/Services/Analytics/` - Sales and customer analytics
- `app/Services/Inventory/` - Stock management
- `app/Services/Invoice/` - Invoice operations
- `app/Services/Order/` - Order processing
- `app/Services/System/` - Health monitoring and system info
- `app/Services/User/` - User management

**Tool Structure**: Every MCP tool must:
- Extend `Laravel\Mcp\Server\Tool`
- Include a descriptive `$description` property (supports markdown)
- Implement `handle(Request $request): Response` method
- Optionally define `schema(): array` for parameter validation
- Inject services via constructor

### Database Models and Relationships

Core models and their relationships:
- **User** ’ hasMany ’ Order, Invoice
- **Order** ’ belongsTo ’ User; hasMany ’ OrderItem, Payment; hasOne ’ Invoice
- **Product** ’ belongsToMany ’ Category; hasMany ’ OrderItem
- **OrderItem** ’ belongsTo ’ Order, Product
- **Invoice** ’ belongsTo ’ Order, User; hasOne ’ Payment
- **Payment** ’ belongsTo ’ Invoice, Order

## Creating New MCP Tools

### Step-by-Step Process

1. **Generate the tool class**:
```bash
./vendor/bin/sail artisan make:mcp-tool GetCustomerMetrics
```

2. **Create or reuse a service** (if business logic is needed):
```bash
# Services are not auto-generated, create manually in app/Services/
# Organize by domain (Analytics, Inventory, etc.)
```

3. **Implement the tool** in `app/Mcp/Tools/`:
```php
<?php

namespace App\Mcp\Tools;

use App\Services\Analytics\CustomerAnalyticsService;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetCustomerMetrics extends Tool
{
    protected string $description = 'Retrieve customer behavior metrics and segmentation data';

    public function __construct(
        private readonly CustomerAnalyticsService $customerService
    ) {}

    public function handle(Request $request): Response
    {
        $customerId = $request->input('customer_id');

        $metrics = $this->customerService->getCustomerMetrics($customerId);

        return Response::text(json_encode($metrics, JSON_PRETTY_PRINT));
    }

    public function schema(): array
    {
        return [
            'customer_id' => [
                'type' => 'integer',
                'description' => 'Customer ID to analyze',
                'required' => false,
            ],
        ];
    }
}
```

4. **Register in `app/Mcp/Servers/LaravelServer.php`**:
```php
protected array $tools = [
    // ... existing tools
    GetCustomerMetrics::class,
];
```

5. **Test with MCP Inspector**:
```bash
./vendor/bin/sail artisan mcp:inspector laravel
# Access http://localhost:6274 and test your new tool
```

## Critical Implementation Notes

### Service Layer Best Practices

- **Always** put business logic in services, never in MCP tools
- Use private methods in services to break down complex operations
- Return structured arrays (not collections) for JSON serialization
- Round monetary values to 2 decimals: `round((float) $value, 2)`
- Include timestamps in service responses: `'timestamp' => now()->toISOString()`

### Database Query Patterns

**Aggregation queries** should use DB facade for efficiency:
```php
$stats = DB::table('orders')
    ->select(
        DB::raw('COUNT(*) as total_orders'),
        DB::raw('SUM(total) as revenue'),
        DB::raw('AVG(total) as avg_order_value')
    )
    ->first();
```

**Date filtering** patterns used throughout:
- Today: `->whereDate('created_at', today())`
- This week: `->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])`
- This month: `->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)`

**Period grouping** for analytics:
```php
DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as period") // daily
DB::raw("DATE_FORMAT(created_at, '%Y-%u') as period")    // weekly
DB::raw("DATE_FORMAT(created_at, '%Y-%m') as period")    // monthly
```

### MCP Response Patterns

Always use `Response::text()` with pretty-printed JSON:
```php
return Response::text(json_encode($data, JSON_PRETTY_PRINT));
```

Never return raw arrays or use `Response::json()` - the MCP protocol expects text content.

## Sample Data

The seeder (`database/seeders/DatabaseSeeder.php`) creates realistic e-commerce data:
- 101 users (100 regular + 1 admin: `admin@example.com`)
- 10 product categories
- 200 products with 1-3 categories each
- 500 orders with 1-5 line items each
- Invoices for shipped/delivered orders only
- Payments for paid invoices only
- ~6 months of historical data (generated with random timestamps)

Order statuses: `pending`, `processing`, `shipped`, `delivered`, `cancelled`
Payment methods: `credit_card`, `debit_card`, `paypal`, `bank_transfer`

## Claude Desktop Integration

Configuration file location varies by OS:
- macOS: `~/Library/Application Support/Claude/claude_desktop_config.json`
- Windows: `%APPDATA%\Claude\claude_desktop_config.json`
- Linux: `~/.config/Claude/claude_desktop_config.json`

Add this configuration (update container name if different):
```json
{
    "mcpServers": {
        "laravel-ecommerce": {
            "command": "docker",
            "args": [
                "exec",
                "-i",
                "laravel-mcp-server-laravel.test-1",
                "php",
                "artisan",
                "mcp:start",
                "laravel"
            ]
        }
    }
}
```

**Verify container name**:
```bash
docker ps --filter "name=laravel.test" --format "{{.Names}}"
```

After updating config, **restart Claude Desktop completely**.

## Common Development Tasks

### Adding Business Logic

1. Identify the appropriate service domain (Analytics, Inventory, Order, etc.)
2. Add method to existing service OR create new service
3. Inject service into relevant MCP tool
4. Test logic in Tinker before integrating:
```bash
./vendor/bin/sail artisan tinker
>>> $service = app(\App\Services\Analytics\SalesAnalyticsService::class);
>>> $service->getSalesStats();
```

### Modifying Database Schema

1. Create migration:
```bash
./vendor/bin/sail artisan make:migration add_discount_to_orders
```

2. Edit migration in `database/migrations/`
3. Run migration:
```bash
./vendor/bin/sail artisan migrate
```

4. Update model with new fields/relationships
5. Update factory if field should be seeded
6. Refresh database to test:
```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

### Debugging MCP Tool Issues

1. **Tool not appearing**: Check registration in `LaravelServer.php`
2. **Parameter errors**: Verify `schema()` method matches expected inputs
3. **Service errors**: Test service directly in Tinker
4. **Database issues**: Check with `./vendor/bin/sail artisan db:show`
5. **Use MCP Inspector**: Visual debugging at http://localhost:6274

## Docker Container Architecture

The application runs in three primary containers defined in `compose.yaml`:
- **laravel.test**: PHP 8.4 Alpine with application code
- **mysql**: MySQL 8.0 database server
- **phpmyadmin**: Database management interface

**Important**: MCP Inspector is started by the `mcp:inspector` command and runs on port 6274 (bound to 0.0.0.0 for external access).

### Container Shell Access

```bash
# General shell access
./vendor/bin/sail shell

# MySQL shell
./vendor/bin/sail mysql

# Run artisan commands
./vendor/bin/sail artisan <command>

# Run composer commands
./vendor/bin/sail composer <command>
```

## Code Quality Standards

- Run Laravel Pint before committing: `./vendor/bin/sail pint`
- Follow PSR-12 coding standards (enforced by Pint)
- Use typed properties and return types
- Prefer readonly properties for injected services
- Use match expressions over switch statements when appropriate
- Include docblocks for public methods in services

## Environment Variables

Key `.env` settings for development:
```env
APP_ENV=local
APP_DEBUG=true
DB_HOST=mysql              # Container service name
DB_PORT=3306               # Internal port
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
FORWARD_DB_PORT=3307       # Host port mapping
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Never commit `.env` files. Use `.env.example` as template.

## Testing Philosophy

- Feature tests for MCP tools can be found in `tests/Feature/`
- Unit tests for services in `tests/Unit/`
- Use factories for test data generation
- Database transactions are used in tests (automatic rollback)
- Test both success and error cases

## Common Gotchas

1. **Container name mismatch**: Claude Desktop config must match actual Docker container name. Check with `docker ps`.
2. **Port conflicts**: If 80, 3307, or 8080 are in use, update `compose.yaml` and `.env` port mappings.
3. **Permission issues**: Storage and cache directories must be writable. Run: `./vendor/bin/sail artisan storage:link`
4. **Seeder data changes**: Always run `migrate:fresh --seed` after modifying seeders, not just `db:seed`.
5. **Service injection**: If tool can't find service, check namespace and constructor parameter type.
6. **JSON serialization**: Use `->toArray()` on collections before returning from services.

## MCP Protocol Specifics

- Tools use STDIO transport when invoked via Docker (Claude Desktop integration)
- HTTP transport available but not used in current setup
- All tool responses must be text (use `Response::text()`)
- Tool names are automatically converted from class names (GetSalesStatsTool ’ get_sales_stats)
- Resources provide static configuration data
- Prompts feature not currently utilized

## Helpful Artisan Commands

```bash
# View all routes
./vendor/bin/sail artisan route:list

# Clear all caches
./vendor/bin/sail artisan optimize:clear

# View MCP server info
./vendor/bin/sail artisan mcp:list

# View application events
./vendor/bin/sail artisan event:list

# Database table details
./vendor/bin/sail artisan db:table orders
```
