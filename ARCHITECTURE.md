# Architecture - Laravel MCP Server

## Table of Contents

- [Overview](#overview)
- [Layered Architecture](#layered-architecture)
- [Service Layer](#service-layer)
- [Dependency Injection](#dependency-injection)
- [Best Practices](#best-practices)
- [Adding New Features](#adding-new-features)

---

## Overview

This project follows a **clean layered architecture** with clear separation of concerns. Business logic is isolated in dedicated service classes, while MCP Tools act as thin controllers that delegate to these services.

## Layered Architecture

```
┌─────────────────────────────────────────────────────┐
│            AI Assistant (Claude)                    │
└──────────────────────┬──────────────────────────────┘
                       │ MCP Protocol
                       │ (STDIO or HTTP)
┌──────────────────────▼──────────────────────────────┐
│              Laravel MCP Server                     │
│                                                      │
│  ┌────────────────────────────────────────────┐    │
│  │         MCP Tools Layer (15 tools)         │    │
│  │  - Request validation                      │    │
│  │  - Schema definition                       │    │
│  │  - Response formatting                     │    │
│  │  - Thin controllers (delegation only)     │    │
│  └──────────────────┬─────────────────────────┘    │
│                     │ Dependency Injection           │
│  ┌──────────────────▼─────────────────────────┐    │
│  │         Business Logic Layer               │    │
│  │                                             │    │
│  │  ┌──────────────────────────────────┐     │    │
│  │  │  User Service                     │     │    │
│  │  │  - User management                │     │    │
│  │  │  - Statistics & search            │     │    │
│  │  └──────────────────────────────────┘     │    │
│  │                                             │    │
│  │  ┌──────────────────────────────────┐     │    │
│  │  │  Analytics Services               │     │    │
│  │  │  - Sales analytics                │     │    │
│  │  │  - Customer insights              │     │    │
│  │  └──────────────────────────────────┘     │    │
│  │                                             │    │
│  │  ┌──────────────────────────────────┐     │    │
│  │  │  Inventory Service                │     │    │
│  │  │  - Stock alerts                   │     │    │
│  │  │  - Product search                 │     │    │
│  │  └──────────────────────────────────┘     │    │
│  │                                             │    │
│  │  ┌──────────────────────────────────┐     │    │
│  │  │  Order & Invoice Services         │     │    │
│  │  └──────────────────────────────────┘     │    │
│  │                                             │    │
│  │  ┌──────────────────────────────────┐     │    │
│  │  │  System Health Service            │     │    │
│  │  └──────────────────────────────────┘     │    │
│  └──────────────────┬─────────────────────────┘    │
│                     │                                │
│  ┌──────────────────▼─────────────────────────┐    │
│  │         Data Access Layer                  │    │
│  │  - Eloquent Models                         │    │
│  │  - Query Builders                          │    │
│  │  - Database Relationships                  │    │
│  └──────────────────┬─────────────────────────┘    │
│                     │                                │
│  ┌──────────────────▼─────────────────────────┐    │
│  │         Infrastructure Layer               │    │
│  │  - MySQL Database                          │    │
│  │  - Cache (Database)                        │    │
│  │  - Queue (Database)                        │    │
│  │  - Sanctum Auth                            │    │
│  └────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────┘
```

### Layer Responsibilities

#### 1. MCP Tools Layer
**Purpose**: Handle HTTP/MCP protocol concerns only

- Validate incoming requests
- Define JSON schemas for parameters
- Format responses
- Delegate all business logic to services
- Keep methods thin (< 10 lines ideally)

**Example**:
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

#### 2. Business Logic Layer (Services)
**Purpose**: Contain all business logic and domain rules

- Process business operations
- Apply business rules and validations
- Coordinate between multiple models
- Return structured data arrays
- No knowledge of HTTP/MCP protocols

**Example**:
```php
class SalesAnalyticsService
{
    public function getSalesStats(): array
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total');

        return [
            'overview' => [
                'total_orders' => $totalOrders,
                'total_revenue' => round($totalRevenue, 2),
            ],
            'timestamp' => now()->toISOString(),
        ];
    }
}
```

#### 3. Data Access Layer (Models)
**Purpose**: Represent database entities and relationships

- Define table structure
- Define relationships (hasMany, belongsTo, etc.)
- Provide query scopes
- Handle attribute casting
- No business logic

#### 4. Infrastructure Layer
**Purpose**: Handle technical concerns

- Database connections
- Caching
- Queue processing
- Authentication

---

## Service Layer

### Available Services

```
app/Services/
├── User/
│   └── UserService.php
├── Analytics/
│   ├── SalesAnalyticsService.php
│   └── CustomerInsightsService.php
├── Inventory/
│   └── InventoryService.php
├── Order/
│   └── OrderService.php
├── Invoice/
│   └── InvoiceService.php
└── System/
    └── SystemHealthService.php
```

### Service Details

#### UserService

**Namespace**: `App\Services\User\UserService`

**Methods**:
```php
// Get comprehensive user statistics
getUserStats(): array

// Search users by name or email
searchUsers(string $query, int $limit = 10): array

// Create a new user with validation
createUser(array $data): array
```

**Used by Tools**:
- GetUserStatsTool
- SearchUsersTool
- CreateUserTool

---

#### SalesAnalyticsService

**Namespace**: `App\Services\Analytics\SalesAnalyticsService`

**Methods**:
```php
// Get comprehensive sales statistics
getSalesStats(): array

// Get revenue breakdown by period (daily/weekly/monthly)
getRevenueByPeriod(string $period = 'daily', int $limit = 30): array

// Get top products by quantity or revenue
getTopProducts(int $limit = 10, string $by = 'revenue'): array
```

**Used by Tools**:
- GetSalesStatsTool
- GetRevenueByPeriodTool (to be refactored)
- GetTopProductsTool

---

#### CustomerInsightsService

**Namespace**: `App\Services\Analytics\CustomerInsightsService`

**Methods**:
```php
// Get customer insights and analytics
getCustomerInsights(int $limit = 20): array

// Determine customer segment
getCustomerSegment(int $orders, float $totalSpent, int $daysSinceLast): string
```

**Customer Segments**:
- **VIP**: Total spent >= €5,000
- **Loyal**: 5+ orders and < €5,000 spent
- **At Risk**: Last order > 90 days ago
- **One-Time**: Only 1 order
- **Regular**: Everyone else

**Used by Tools**:
- GetCustomerInsightsTool

---

#### InventoryService

**Namespace**: `App\Services\Inventory\InventoryService`

**Methods**:
```php
// Get inventory alerts
getInventoryAlerts(): array

// Search products with inventory details
searchProducts(string $query, int $limit = 20): array
```

**Alert Types**:
- Low stock products
- Out of stock products
- Inactive products with stock
- Overdue invoices

**Used by Tools**:
- GetInventoryAlertsTool
- GetProductInventoryTool (to be refactored)

---

#### OrderService

**Namespace**: `App\Services\Order\OrderService`

**Methods**:
```php
// Search orders with flexible filters
searchOrders(array $filters): array

// Get order statistics
getOrderStatistics(): array
```

**Filter Options**:
- `status`: Order status filter
- `start_date`, `end_date`: Date range
- `min_amount`: Minimum order amount
- `customer_id`: Filter by customer
- `limit`: Results limit

**Used by Tools**:
- SearchOrdersTool (to be refactored)

---

#### InvoiceService

**Namespace**: `App\Services\Invoice\InvoiceService`

**Methods**:
```php
// Get invoice details with payment tracking
getInvoiceDetails(int $invoiceId): array

// Get invoice statistics
getInvoiceStatistics(): array
```

**Used by Tools**:
- GetInvoiceDetailsTool (to be refactored)

---

#### SystemHealthService

**Namespace**: `App\Services\System\SystemHealthService`

**Methods**:
```php
// Perform application health check
checkHealth(): array

// Get system information
getSystemInfo(): array

// Get database information
getDatabaseInfo(): array
```

**Used by Tools**:
- HealthCheckTool
- GetSystemInfoTool
- GetDatabaseInfoTool

---

## Dependency Injection

### How It Works

Laravel's service container automatically resolves dependencies when tools are instantiated:

```php
// 1. Tool declares service dependency
class GetSalesStatsTool extends Tool
{
    public function __construct(
        private readonly SalesAnalyticsService $salesAnalyticsService
    ) {}
}

// 2. Laravel automatically injects the service
// No manual instantiation needed!
```

### Benefits

1. **Testability**: Easy to mock services in tests
2. **Flexibility**: Easy to swap implementations
3. **Decoupling**: Tools don't know how services are created
4. **Type Safety**: PHP 8+ constructor property promotion with types

### Testing Example

```php
// In a test
$mockService = Mockery::mock(SalesAnalyticsService::class);
$mockService->shouldReceive('getSalesStats')
    ->once()
    ->andReturn(['total' => 100]);

app()->instance(SalesAnalyticsService::class, $mockService);

$tool = app(GetSalesStatsTool::class);
// Tool now uses the mocked service
```

---

## Best Practices

### 1. Keep Tools Thin

**❌ Bad** - Business logic in tool:
```php
class GetSalesStatsTool extends Tool
{
    public function handle(Request $request): Response
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total');
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // More logic...

        return Response::text(json_encode($data, JSON_PRETTY_PRINT));
    }
}
```

**✅ Good** - Delegation to service:
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

### 2. Services Return Arrays

Services should return plain arrays, not Response objects:

**✅ Good**:
```php
public function getSalesStats(): array
{
    return [
        'total_orders' => 500,
        'total_revenue' => 100000.50,
    ];
}
```

**❌ Bad**:
```php
public function getSalesStats(): Response
{
    // Don't return HTTP responses from services
}
```

### 3. Use Type Hints

Always use type hints for parameters and return types:

```php
public function searchUsers(string $query, int $limit = 10): array
{
    // Implementation
}
```

### 4. Group Related Methods

Organize services by domain, not by CRUD operations:

**✅ Good**:
```php
// SalesAnalyticsService.php
- getSalesStats()
- getRevenueByPeriod()
- getTopProducts()
```

**❌ Bad**:
```php
// OrderCrudService.php (too generic)
- createOrder()
- updateOrder()
- deleteOrder()
```

### 5. Return Consistent Structures

Always include a timestamp and use consistent key names:

```php
return [
    'data' => [...],
    'total' => 100,
    'timestamp' => now()->toISOString(),
];
```

---

## Adding New Features

### Step 1: Create a Service

```bash
# Create the directory if needed
mkdir -p app/Services/YourDomain

# Create the service file
touch app/Services/YourDomain/YourService.php
```

```php
<?php

namespace App\Services\YourDomain;

class YourService
{
    public function yourMethod(): array
    {
        // Business logic here

        return [
            'data' => [...],
            'timestamp' => now()->toISOString(),
        ];
    }
}
```

### Step 2: Create an MCP Tool

```bash
./vendor/bin/sail artisan make:mcp-tool YourTool
```

```php
<?php

namespace App\Mcp\Tools;

use App\Services\YourDomain\YourService;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class YourTool extends Tool
{
    protected string $description = 'Your tool description';

    public function __construct(
        private readonly YourService $yourService
    ) {}

    public function handle(Request $request): Response
    {
        $result = $this->yourService->yourMethod();
        return Response::text(json_encode($result, JSON_PRETTY_PRINT));
    }
}
```

### Step 3: Test

```bash
# Test the service directly
./vendor/bin/sail artisan tinker
>>> $service = app(App\Services\YourDomain\YourService::class);
>>> $service->yourMethod();

# Test the tool
./vendor/bin/sail artisan tinker
>>> $tool = app(App\Mcp\Tools\YourTool::class);
```

---

## Advantages of This Architecture

### 1. Testability
- Services can be tested independently
- Easy to mock dependencies
- No HTTP/MCP protocol concerns in business logic

### 2. Reusability
- Services can be used by MCP tools, API controllers, Artisan commands
- No code duplication

### 3. Maintainability
- Clear separation of concerns
- Easy to locate business logic
- Consistent patterns throughout the codebase

### 4. Flexibility
- Easy to swap implementations
- Can add caching, logging, etc. in one place
- Services can call other services

### 5. Scalability
- New features follow established patterns
- Team members can work independently on different services
- Easy to understand for new developers

---

## Related Documentation

- [README.md](README.md) - Project overview and quick start
- [Testing Guide](#) - How to write tests for services
- [API Documentation](#) - REST API endpoints

---

**Built with clean architecture principles**
**Last updated**: November 2025
