<div align="center">

# Laravel MCP Server

### Enterprise-Grade AI Integration for E-commerce Analytics

**Empower AI assistants with direct access to your business intelligence, sales data, and system analytics through the Model Context Protocol.**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com)
[![Docker](https://img.shields.io/badge/Docker-Sail-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://laravel.com/docs/sail)
[![MCP](https://img.shields.io/badge/MCP-Protocol-5C2D91?style=for-the-badge)](https://modelcontextprotocol.io)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](https://opensource.org/licenses/MIT)

[Features](#-features) • [Quick Start](#-quick-start) • [Documentation](#-documentation) • [Architecture](#-architecture)

</div>

---

## Overview

A production-ready **Laravel MCP (Model Context Protocol) server** that bridges the gap between AI assistants like Claude and your e-commerce business data. Enable natural language queries to access sales analytics, inventory management, customer insights, and system monitoring—all through a secure, structured protocol.

### What Makes This Special?

-   **15 Production-Ready MCP Tools** - Comprehensive e-commerce operations coverage
-   **Clean Architecture** - Service layer pattern with dependency injection
-   **Complete E-commerce Dataset** - 500+ orders, 200 products, realistic analytics
-   **Docker-First** - Fully containerized with Laravel Sail
-   **Real-Time Analytics** - Revenue tracking, customer segmentation, inventory alerts
-   **Developer-Friendly** - Auto-discovery, MCP Inspector integration, extensive documentation
-   **Battle-Tested Stack** - Laravel 12, PHP 8.4, MySQL 8.0

---

## Table of Contents

-   [Features](#features)
-   [Quick Start](#quick-start)
-   [Tech Stack](#tech-stack)
-   [Architecture](#architecture)
-   [Configuration](#configuration)
-   [Usage Examples](#usage-examples)
-   [Development](#development)
-   [Testing](#testing)
-   [Deployment](#deployment)
-   [Troubleshooting](#troubleshooting)
-   [Contributing](#contributing)

---

## Features

### MCP Tools Overview

<details open>
<summary><b>User Management (3 Tools)</b></summary>

| Tool             | Description                               | Key Parameters              |
| ---------------- | ----------------------------------------- | --------------------------- |
| `get_user_stats` | Retrieve user statistics and distribution | None                        |
| `search_users`   | Search users by name or email             | `query`, `limit`            |
| `create_user`    | Create new user accounts                  | `name`, `email`, `password` |

</details>

<details open>
<summary><b>Sales Analytics (4 Tools)</b></summary>

| Tool                    | Description                             | Key Parameters                   |
| ----------------------- | --------------------------------------- | -------------------------------- |
| `get_sales_stats`       | Comprehensive sales dashboard with KPIs | None                             |
| `get_revenue_by_period` | Revenue breakdown and trends            | `period` (daily/weekly/monthly)  |
| `get_top_products`      | Best-selling products analysis          | `limit`, `by` (quantity/revenue) |
| `get_customer_insights` | Customer segmentation and LTV           | None                             |

</details>

<details open>
<summary><b>Inventory & Alerts (2 Tools)</b></summary>

| Tool                    | Description                             | Key Parameters   |
| ----------------------- | --------------------------------------- | ---------------- |
| `get_inventory_alerts`  | Low stock warnings and overdue invoices | None             |
| `get_product_inventory` | Product search with stock levels        | `query`, `limit` |

</details>

<details>
<summary><b>Order Management (2 Tools)</b></summary>

| Tool                  | Description                            | Key Parameters                                        |
| --------------------- | -------------------------------------- | ----------------------------------------------------- |
| `search_orders`       | Advanced order filtering and search    | `status`, `date_range`, `amount_range`, `customer_id` |
| `get_invoice_details` | Detailed invoice with payment tracking | `invoice_id`                                          |

</details>

<details>
<summary><b>System Monitoring (3 Tools)</b></summary>

| Tool                | Description                    | Key Parameters |
| ------------------- | ------------------------------ | -------------- |
| `get_system_info`   | Laravel/PHP/Server information | None           |
| `get_database_info` | Database schema and row counts | None           |
| `health_check`      | Application health monitoring  | None           |

</details>

### MCP Resources

| Resource         | URI                     | Description                        |
| ---------------- | ----------------------- | ---------------------------------- |
| **App Settings** | `config://app/settings` | Application configuration snapshot |
| **Laravel Info** | `system://laravel/info` | Framework runtime information      |

### Sample Dataset

Pre-seeded with realistic e-commerce data:

```
101 Users  •  10 Categories  •  200 Products  •  500 Orders  •  1,472 Line Items
Total Revenue: €929,574.57  •  6 Months Historical Data
```

---

## Quick Start

### Prerequisites

-   **Docker Desktop** - [Download here](https://www.docker.com/products/docker-desktop)
-   **Composer** - [Install guide](https://getcomposer.org/download/)
-   **MCP-Compatible AI Client** - [Claude Desktop](https://claude.ai/download), Cursor, or [other clients](https://modelcontextprotocol.io/clients)

### Installation

```bash
# Clone the repository
git clone https://github.com/yourusername/laravel-mcp-server.git
cd laravel-mcp-server

# Install dependencies and configure environment
composer install
cp .env.example .env

# Build and start Docker containers
./vendor/bin/sail build --no-cache
./vendor/bin/sail up -d

# Initialize database with sample data
./vendor/bin/sail artisan migrate:fresh --seed
```

### Verify Installation

```bash
# Check container status
./vendor/bin/sail ps

# Test MCP tools with the inspector
./vendor/bin/sail artisan mcp:inspector laravel
# Open http://localhost:6274 in your browser
```

**Access Points:**

-   Application: http://localhost
-   phpMyAdmin: http://localhost:8080 (user: `sail`, password: `password`)
-   MCP Inspector: http://localhost:6274

---

## Tech Stack

| Technology          | Version    | Purpose                        |
| ------------------- | ---------- | ------------------------------ |
| **Laravel**         | 12.x       | Application framework          |
| **PHP**             | 8.4 Alpine | Runtime environment            |
| **MySQL**           | 8.0        | Relational database            |
| **Laravel MCP**     | 0.3.2+     | MCP server implementation      |
| **Laravel Sail**    | 1.47+      | Docker development environment |
| **Laravel Sanctum** | 4.0+       | API authentication             |
| **Laravel Breeze**  | 2.3+       | Authentication scaffolding     |

---

## Architecture

### System Overview

```
┌─────────────────────────────────────────────┐
│        AI Assistant (Claude Desktop)         │
│           via MCP Protocol                   │
└──────────────────┬──────────────────────────┘
                   │ STDIO / HTTP
┌──────────────────▼──────────────────────────┐
│         Laravel MCP Server Layer            │
│  ┌─────────────────────────────────────┐   │
│  │    MCP Tools (15 tools)             │   │
│  │  • Request validation               │   │
│  │  • Schema definition                │   │
│  │  • Response formatting              │   │
│  └──────────────┬──────────────────────┘   │
│                 │ Dependency Injection       │
│  ┌──────────────▼──────────────────────┐   │
│  │    Business Logic Services          │   │
│  │  • UserService                      │   │
│  │  • SalesAnalyticsService            │   │
│  │  • CustomerInsightsService          │   │
│  │  • InventoryService                 │   │
│  │  • OrderService                     │   │
│  │  • InvoiceService                   │   │
│  │  • SystemHealthService              │   │
│  └──────────────┬──────────────────────┘   │
│                 │                            │
│  ┌──────────────▼──────────────────────┐   │
│  │    Data Access Layer                │   │
│  │  • Eloquent Models & Relationships  │   │
│  │  • Query Builders                   │   │
│  └──────────────┬──────────────────────┘   │
│                 │                            │
│  ┌──────────────▼──────────────────────┐   │
│  │    Infrastructure                   │   │
│  │  • MySQL Database                   │   │
│  │  • Cache (Database)                 │   │
│  │  • Queue (Database)                 │   │
│  └─────────────────────────────────────┘   │
└─────────────────────────────────────────────┘
```

### Architecture Principles

**Separation of Concerns**

-   MCP Tools act as thin controllers, handling only request/response
-   Services contain all business logic, testable and reusable
-   Models represent data structures and database relationships

**Dependency Injection**

-   Services automatically injected into tools via Laravel's container
-   Promotes testability and maintainability
-   Loose coupling between components

**Single Responsibility**

-   Each service focuses on a specific domain (User, Analytics, Inventory)
-   Methods are focused, cohesive, and composable

For detailed architecture documentation, see [ARCHITECTURE.md](ARCHITECTURE.md).

---

## Configuration

### Claude Desktop Integration

**Location:**

-   **macOS:** `~/Library/Application Support/Claude/claude_desktop_config.json`
-   **Windows:** `%APPDATA%\Claude\claude_desktop_config.json`
-   **Linux:** `~/.config/Claude/claude_desktop_config.json`

**Configuration:**

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

**Important:** Restart Claude Desktop after adding this configuration.

### Environment Configuration

Key settings in `.env`:

```env
# Application
APP_NAME=Laravel
APP_ENV=local
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
FORWARD_DB_PORT=3307

# Cache & Session
CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_DRIVER=database

# External Access
FORWARD_PHPMYADMIN_PORT=8080
```

### Docker Services

The application uses three primary services:

| Service           | Port | Description                       |
| ----------------- | ---- | --------------------------------- |
| **laravel.test**  | 80   | Main application (PHP 8.4 Alpine) |
| **mysql**         | 3307 | MySQL 8.0 database server         |
| **phpmyadmin**    | 8080 | Database management interface     |
| **MCP Inspector** | 6274 | Tool testing and debugging UI     |

---

## Usage Examples

### Natural Language Queries

Once connected to Claude Desktop, interact using natural language:

**Sales & Revenue**

```
"What are my total sales for this quarter?"
"Show me the top 5 products by revenue"
"What's the revenue breakdown by month?"
"How are my sales trending week over week?"
```

**Inventory Management**

```
"What products are low on stock?"
"Show me all overdue invoices"
"Search for products containing 'laptop'"
"What inventory alerts do I have?"
```

**Customer Analysis**

```
"Give me customer insights and segments"
"Who are my top customers by total spend?"
"What's the average customer lifetime value?"
```

**Operations**

```
"Find all pending orders"
"Show orders from the last 30 days over €1000"
"Get invoice details for invoice #42"
"Check application health status"
```

**User Management**

```
"How many users are registered?"
"Search for users with email containing 'gmail'"
"Create a new user for John Smith"
```

---

## Development

### Project Structure

```
laravel-mcp-server/
├── app/
│   ├── Http/Controllers/       # HTTP controllers
│   ├── Models/                 # Eloquent models (User, Product, Order, etc.)
│   ├── Mcp/
│   │   ├── Tools/              # 15 MCP tool implementations
│   │   ├── Resources/          # 2 MCP resources
│   │   └── Servers/            # MCP server registration
│   └── Services/               # Business logic layer
│       ├── Analytics/          # Sales & customer analytics
│       ├── Inventory/          # Stock management
│       ├── Invoice/            # Invoice operations
│       ├── Order/              # Order processing
│       ├── System/             # Health monitoring
│       └── User/               # User management
├── database/
│   ├── factories/              # Model factories for testing
│   ├── migrations/             # Database schema
│   └── seeders/                # Sample data seeders
├── docker/                     # Docker configuration
├── routes/
│   ├── ai.php                  # MCP server routes
│   ├── api.php                 # API endpoints
│   └── web.php                 # Web routes
├── tests/
│   ├── Feature/                # Integration tests
│   └── Unit/                   # Unit tests
└── compose.yaml                # Docker Compose configuration
```

### Creating New MCP Tools

**1. Generate the tool class:**

```bash
./vendor/bin/sail artisan make:mcp-tool GetProductReport
```

**2. Implement the tool:**

```php
<?php

namespace App\Mcp\Tools;

use App\Services\Analytics\SalesAnalyticsService;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetProductReport extends Tool
{
    protected string $description = 'Generate comprehensive product sales report';

    public function __construct(
        private SalesAnalyticsService $salesService
    ) {}

    public function handle(Request $request): Response
    {
        $productId = $request->input('product_id');

        $report = $this->salesService->getProductReport($productId);

        return Response::text(json_encode($report, JSON_PRETTY_PRINT));
    }

    public function schema(): array
    {
        return [
            'product_id' => [
                'type' => 'integer',
                'description' => 'Product ID to generate report for',
                'required' => true,
            ],
        ];
    }
}
```

**3. Register in `app/Mcp/Servers/LaravelServer.php`:**

```php
protected array $tools = [
    // ... existing tools
    GetProductReport::class,
];
```

**4. Test your tool:**

```bash
./vendor/bin/sail artisan mcp:inspector laravel
# Access http://localhost:6274
```

### Common Development Commands

**Application Management:**

```bash
# Start containers
./vendor/bin/sail up -d

# Stop containers
./vendor/bin/sail down

# View logs
./vendor/bin/sail logs -f

# Access container shell
./vendor/bin/sail shell
```

**Database Operations:**

```bash
# Run migrations
./vendor/bin/sail artisan migrate

# Fresh migration with sample data
./vendor/bin/sail artisan migrate:fresh --seed

# Access MySQL CLI
./vendor/bin/sail mysql

# Database status
./vendor/bin/sail artisan db:show
```

**Code Quality:**

```bash
# Run tests
./vendor/bin/sail test

# Code formatting
./vendor/bin/sail pint

# Interactive REPL
./vendor/bin/sail artisan tinker
```

---

## Testing

### MCP Inspector (Recommended)

Interactive web-based tool testing:

```bash
./vendor/bin/sail artisan mcp:inspector laravel
```

Access at http://localhost:6274 to:

-   View all registered tools and resources
-   Test tools with custom parameters
-   Inspect request/response data
-   Debug schema validation

### Automated Testing

```bash
# Run all tests
./vendor/bin/sail test

# Run with coverage
./vendor/bin/sail artisan test --coverage

# Run specific test
./vendor/bin/sail artisan test tests/Feature/Auth/AuthenticationTest.php
```

### Manual Service Testing

```bash
./vendor/bin/sail artisan tinker

>>> $userService = app(\App\Services\User\UserService::class);
>>> $userService->getUserStats();

>>> $salesService = app(\App\Services\Analytics\SalesAnalyticsService::class);
>>> $salesService->getSalesStats();
```

---

## Deployment

### Production Checklist

**Environment Configuration:**

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_GENERATED_KEY

# Production database
DB_HOST=your-production-host
DB_DATABASE=your-production-db
DB_USERNAME=your-production-user
DB_PASSWORD=strong-password

# Use Redis for better performance
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Security
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict
```

**Optimization Commands:**

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### Process Management

For HTTP-based MCP deployments, use Supervisor:

```ini
[program:laravel-mcp]
process_name=%(program_name)s
command=php /var/www/artisan mcp:start laravel
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/laravel-mcp.log
```

**Note:** Claude Desktop integration uses STDIO transport via Docker, not HTTP.

---

## Troubleshooting

### Common Issues

<details>
<summary><b>Docker containers won't start</b></summary>

```bash
./vendor/bin/sail down
docker system prune -f
./vendor/bin/sail build --no-cache
./vendor/bin/sail up -d
```

</details>

<details>
<summary><b>MCP tools not appearing in Claude Desktop</b></summary>

1. Verify Docker container is running: `./vendor/bin/sail ps`
2. Check container name matches config: `docker ps`
3. Restart Claude Desktop completely
4. Test with MCP Inspector: `./vendor/bin/sail artisan mcp:inspector laravel`

</details>

<details>
<summary><b>Permission errors</b></summary>

```bash
./vendor/bin/sail artisan storage:link
sudo chown -R $USER:$USER storage bootstrap/cache
```

</details>

<details>
<summary><b>Database connection issues</b></summary>

```bash
# Check MySQL service
./vendor/bin/sail ps

# View MySQL logs
./vendor/bin/sail logs mysql

# Test connection
./vendor/bin/sail mysql -u sail -p
```

</details>

<details>
<summary><b>Clear all caches</b></summary>

```bash
./vendor/bin/sail artisan optimize:clear
```

</details>

### Getting Help

-   Check the [MCP Specification](https://modelcontextprotocol.io)
-   Review [Laravel MCP documentation](https://github.com/laravel/mcp)
-   Inspect tool schemas with MCP Inspector
-   Check Docker logs: `./vendor/bin/sail logs`

---

## Security Considerations

-   **Database Access:** MCP tools have direct database access—implement authorization in production
-   **Input Validation:** All inputs validated using schema definitions
-   **Rate Limiting:** Consider implementing for HTTP-based transports
-   **HTTPS:** Enable SSL/TLS in production environments
-   **Authentication:** Use API tokens for HTTP MCP endpoints
-   **Audit Logs:** Monitor and log all MCP tool invocations
-   **Environment Variables:** Never commit `.env` to version control

---

## Contributing

Contributions are welcome! Please follow these guidelines:

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/amazing-feature`)
3. **Commit** your changes (`git commit -m 'Add amazing feature'`)
4. **Test** thoroughly (`./vendor/bin/sail test`)
5. **Format** code (`./vendor/bin/sail pint`)
6. **Push** to the branch (`git push origin feature/amazing-feature`)
7. **Open** a Pull Request

---

## License

This project is licensed under the **MIT License**. See [LICENSE](https://opensource.org/licenses/MIT) for details.

---

## Resources

-   **Laravel MCP Package:** [github.com/laravel/mcp](https://github.com/laravel/mcp)
-   **MCP Specification:** [modelcontextprotocol.io](https://modelcontextprotocol.io)
-   **MCP Clients:** [modelcontextprotocol.io/clients](https://modelcontextprotocol.io/clients)
-   **Laravel Documentation:** [laravel.com/docs](https://laravel.com/docs)
-   **Laravel Sail:** [laravel.com/docs/sail](https://laravel.com/docs/sail)

---

<div align="center">

**Built with Laravel 12 • PHP 8.4 • MySQL 8.0 • Laravel MCP**

_Empowering AI-driven business intelligence_

Made with ❤️ by the Laravel Community

</div>
