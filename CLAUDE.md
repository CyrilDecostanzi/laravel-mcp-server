# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is an advanced Laravel 12 MCP (Model Context Protocol) server that provides AI assistants with powerful access to e-commerce business data. The server now exposes **24 MCP tools** including:
- **Advanced analytics**: RFM customer segmentation, sales forecasting, product recommendations
- **Read operations**: Sales stats, inventory alerts, customer insights, trending products
- **Write operations**: Create orders, update stock, apply discounts, create products, update order status

All tools follow a clean service-oriented architecture with strict separation of concerns.

## Development Environment

This project uses Docker via Laravel Sail. All commands must be run through Sail:

```bash
# Start containers (always run this first)
./vendor/bin/sail up -d

# Stop containers
./vendor/bin/sail down

# Run any artisan command
./vendor/bin/sail artisan [command]

# Run tests
./vendor/bin/sail test

# Format code
./vendor/bin/sail pint

# Access container shell
./vendor/bin/sail shell

# Access MySQL
./vendor/bin/sail mysql

# View logs
./vendor/bin/sail logs -f
```

## Database Management

```bash
# Run migrations
./vendor/bin/sail artisan migrate

# Fresh database with sample e-commerce data (500+ orders, 200 products)
./vendor/bin/sail artisan migrate:fresh --seed

# Access database
./vendor/bin/sail mysql

# Interactive shell for testing
./vendor/bin/sail artisan tinker
```

## Testing MCP Tools

```bash
# Launch the MCP Inspector web UI (recommended for testing tools)
./vendor/bin/sail artisan mcp:inspector laravel
# Then open http://localhost:6274 in browser

# Test service directly in Tinker
./vendor/bin/sail artisan tinker
>>> $service = app(\App\Services\Analytics\SalesAnalyticsService::class);
>>> $service->getSalesStats();

# Run test suite
./vendor/bin/sail test

# Run specific test file
./vendor/bin/sail artisan test tests/Feature/Auth/AuthenticationTest.php
```

## Architecture Principles

**This codebase follows a strict layered architecture - maintain these patterns:**

### Layer 1: MCP Tools (`app/Mcp/Tools/`)
- **Thin controllers** - no business logic
- Only handle request/response formatting
- Inject services via constructor dependency injection
- Maximum ~10-15 lines in `handle()` method
- Return `Response::text(json_encode($result, JSON_PRETTY_PRINT))`

Example:
```php
class GetSalesStatsTool extends Tool
{
    public function __construct(
        private readonly SalesAnalyticsService $salesAnalyticsService
    ) {}

    public function handle(Request $request): Response
    {
        $result = $this->salesAnalyticsService->getSalesStats();
        return Response::text(json_encode($result, JSON_PRETTY_PRINT));
    }
}
```

### Layer 2: Services (`app/Services/`)
- **All business logic lives here**
- Return plain arrays (never Response objects)
- Use type hints for all parameters and return types
- Include timestamps: `'timestamp' => now()->toISOString()`
- Organized by domain: `User/`, `Analytics/`, `Inventory/`, `Order/`, `Invoice/`, `System/`

Available services:

**Analytics Services:**
- `App\Services\Analytics\SalesAnalyticsService` - Sales statistics and revenue trends
- `App\Services\Analytics\CustomerInsightsService` - Customer segmentation, LTV
- `App\Services\Analytics\RfmAnalysisService` - **NEW** RFM customer segmentation with actionable insights
- `App\Services\Analytics\SalesForecastService` - **NEW** Sales forecasting with trend analysis
- `App\Services\Analytics\ProductRecommendationService` - **NEW** Intelligent product recommendations (collaborative filtering, cross-sell, upsell)

**Order & Inventory Services:**
- `App\Services\Order\OrderService` - Order searching and filtering
- `App\Services\Order\OrderCreationService` - **NEW** Create orders with validation and stock management
- `App\Services\Inventory\InventoryService` - Stock alerts, product search
- `App\Services\Inventory\InventoryManagementService` - **NEW** Stock updates, product creation, discount application
- `App\Services\Invoice\InvoiceService` - Invoice details and payment tracking

**User & System Services:**
- `App\Services\User\UserService` - User management
- `App\Services\System\SystemHealthService` - Health checks, system info

### Layer 3: Models (`app/Models/`)
- Database entities and relationships
- Available models: User, Product, Category, Order, OrderItem, Invoice, Payment

### MCP Server Registration (`routes/ai.php`)
- Tools are registered in `app/Mcp/Servers/LaravelServer.php`
- Two transports available: local (STDIO for Claude Desktop) and web (HTTP)

## Creating New MCP Tools

Follow these steps exactly:

1. **Create the service first** (if needed):
```bash
mkdir -p app/Services/YourDomain
# Create service class with business logic
```

2. **Generate the MCP tool**:
```bash
./vendor/bin/sail artisan make:mcp-tool YourToolName
```

3. **Implement the tool** - inject service, delegate logic:
```php
<?php

namespace App\Mcp\Tools;

use App\Services\YourDomain\YourService;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class YourToolName extends Tool
{
    protected string $description = 'Clear description for AI';

    public function __construct(
        private readonly YourService $yourService
    ) {}

    public function handle(Request $request): Response
    {
        // Extract parameters
        $param = $request->get('param_name');

        // Delegate to service
        $result = $this->yourService->yourMethod($param);

        // Return formatted response
        return Response::text(json_encode($result, JSON_PRETTY_PRINT));
    }

    public function schema(): array
    {
        return [
            'param_name' => [
                'type' => 'string',
                'description' => 'Parameter description',
                'required' => true,
            ],
        ];
    }
}
```

4. **Register in `app/Mcp/Servers/LaravelServer.php`**:
```php
protected array $tools = [
    // ... existing tools
    YourToolName::class,
];
```

5. **Test with MCP Inspector**:
```bash
./vendor/bin/sail artisan mcp:inspector laravel
```

## Code Quality Standards

- Always run Pint for formatting: `./vendor/bin/sail pint`
- Use PHP 8.4 features (constructor property promotion, readonly, match expressions)
- Use Eloquent for database queries (avoid raw SQL unless necessary)
- Always use dependency injection, never instantiate services manually
- Keep methods focused and single-purpose
- Use meaningful variable names

## Key File Locations

- **MCP Tools**: `app/Mcp/Tools/*.php`
- **Business Services**: `app/Services/**/*.php`
- **Models**: `app/Models/*.php`
- **MCP Server Config**: `app/Mcp/Servers/LaravelServer.php`
- **MCP Routes**: `routes/ai.php`
- **Migrations**: `database/migrations/*.php`
- **Seeders**: `database/seeders/*.php`

## Database Schema

Core e-commerce tables:
- `users` - User accounts
- `categories` - Product categories
- `products` - Product catalog with SKU, price, stock
- `orders` - Customer orders with status (pending, processing, completed, cancelled, failed)
- `order_items` - Line items in orders
- `invoices` - Invoice records linked to orders
- `payments` - Payment transactions with status (pending, completed, failed) and methods (credit_card, paypal, bank_transfer, etc.)

## Available MCP Tools (24 Total)

**Advanced Analytics (3 tools):**
- `get_rfm_analysis` - RFM customer segmentation (Champions, Loyal, At Risk, etc.)
- `get_sales_forecast` - Predict future sales with confidence metrics
- `get_customer_insights` - Customer analytics and LTV

**Product Intelligence (3 tools):**
- `get_product_recommendations` - Personalized recommendations (for_customer, cross_sell, upsell)
- `get_trending_products` - Trending products by sales velocity
- `get_top_products` - Best-selling products by quantity or revenue

**Sales Analytics (2 tools):**
- `get_sales_stats` - Comprehensive sales dashboard
- `get_revenue_by_period` - Revenue breakdown (daily/weekly/monthly)

**Order Management - READ & WRITE (4 tools):**
- `search_orders` - Search with flexible filters
- `get_invoice_details` - Detailed invoice info
- `create_order` - **CREATE** new orders with validation
- `update_order_status` - **UPDATE** order status

**Inventory Management - READ & WRITE (5 tools):**
- `get_inventory_alerts` - Low stock and overdue invoice alerts
- `get_product_inventory` - Product search with stock details
- `update_product_stock` - **UPDATE** stock levels (set/add/subtract)
- `create_product` - **CREATE** new products with auto-SKU
- `apply_discount` - **UPDATE** apply percentage discounts

**User Management (3 tools):**
- `get_user_stats` - User statistics
- `search_users` - Search by name/email
- `create_user` - **CREATE** new user accounts

**System Tools (3 tools):**
- `get_system_info` - System information
- `get_database_info` - Database statistics
- `health_check` - Health monitoring

## Important Notes

- **Never bypass Sail** - all commands must run through `./vendor/bin/sail`
- The container name is `laravel-mcp-server-laravel.test-1`
- Database credentials: user=`sail`, password=`password`, database=`laravel`, host=`mysql`
- phpMyAdmin available at http://localhost:8080
- MCP Inspector at http://localhost:6274
- **Always use services for business logic** - never put complex queries or calculations in Tools
- The sample dataset includes 6 months of realistic data with 500+ orders
- **Version 3.0.0**: Now includes predictive analytics, recommendations, and full CRUD operations
- RFM Segments: Champions, Loyal Customers, Potential Loyalists, At Risk, Can't Lose Them, Hibernating, Lost, New Customers, Promising
- Customer Insights Segments: VIP (€5K+), Loyal (5+ orders), At Risk (90+ days), One-Time, Regular
- For detailed architecture, see `ARCHITECTURE.md`

## Demo Scenarios

**Impressive analytics demos:**
1. "Segment my customers using RFM analysis and give me actionable recommendations"
2. "Forecast my sales for the next 7 days and explain the trends"
3. "What products should I recommend to customer #5?"
4. "Show me cross-sell opportunities for product #10"
5. "What are the trending products this week?"

**Action/write operation demos:**
1. "Create a new order for customer #3 with products #5 (qty 2) and #10 (qty 1)"
2. "Update the stock for product #15 to 100 units"
3. "Apply a 20% discount to product #25"
4. "Create a new product called 'Premium Widget' priced at €99.99"
5. "Update order #150 status to 'completed'"

**Complex workflow demos:**
1. "Show me at-risk customers and recommend products to win them back"
2. "Find low stock products and create a purchase order workflow"
3. "Analyze sales forecast, identify growing categories, then show cross-sell opportunities"
