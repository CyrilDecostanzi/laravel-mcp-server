# Laravel E-commerce MCP Server

> A **Laravel MCP server** providing comprehensive business intelligence, analytics, and data management tools for AI assistants like Claude.

[![Laravel](https://img.shields.io/badge/Laravel-12.36.1-FF2D20?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4.14-777BB4?logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0.32-4479A1?logo=mysql)](https://www.mysql.com)
[![Docker](https://img.shields.io/badge/Docker-Sail-2496ED?logo=docker)](https://laravel.com/docs/sail)
[![MCP](https://img.shields.io/badge/MCP-php--mcp%2Flaravel-blue)](https://github.com/php-mcp/laravel)

---

## Table of Contents

- [Overview](#overview)
- [Quick Start](#quick-start)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
  - [Testing the Setup](#testing-the-setup)
- [Features](#features)
  - [MCP Tools (15)](#mcp-tools-15)
  - [MCP Resources (2)](#mcp-resources-2)
  - [E-commerce Database](#e-commerce-database)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Configuration](#configuration)
  - [Claude Desktop Setup](#claude-desktop-setup)
  - [Docker Configuration](#docker-configuration)
  - [Environment Variables](#environment-variables)
- [Usage](#usage)
  - [Starting the Server](#starting-the-server)
  - [MCP Commands](#mcp-commands)
  - [Example Interactions](#example-interactions)
- [Development](#development)
  - [Project Structure](#project-structure)
  - [Adding New MCP Tools](#adding-new-mcp-tools)
  - [Adding New Resources](#adding-new-resources)
  - [Common Commands](#common-commands)
- [Database](#database)
  - [Schema Overview](#schema-overview)
  - [External Access](#external-access)
  - [Migrations & Seeding](#migrations--seeding)
- [Testing](#testing)
  - [MCP Tools Testing](#mcp-tools-testing)
  - [Laravel Tests](#laravel-tests)
- [API & Authentication](#api--authentication)
- [Troubleshooting](#troubleshooting)
- [Production Deployment](#production-deployment)
- [Security Considerations](#security-considerations)
- [Contributing](#contributing)
- [License](#license)

---

## Overview

This Laravel application implements a fully functional **Model Context Protocol (MCP) server** that exposes e-commerce business logic, analytics, and system information to AI assistants. Claude can directly interact with your database, perform analytics, manage inventory, and monitor system health through structured tools and resources.

**Key Capabilities:**
- 15 production-ready MCP tools for e-commerce operations
- **Clean architecture with service layer** - Business logic separated from MCP tools
- Real-time sales analytics and revenue tracking
- Inventory management and alerts
- Customer insights and segmentation
- System health monitoring
- **Dependency injection** - Services automatically injected into tools
- Auto-discovery of tools using PHP 8 attributes
- Multiple transport protocols (STDIO, HTTP)

---

## Quick Start

### Prerequisites

- **Docker** installed and running
- **Composer** (for local development)
- **Claude Desktop** (optional, for AI integration)

### Installation

```bash
# 1. Clone the repository
git clone <repo-url> laravel-mcp-server
cd laravel-mcp-server

# 2. Install dependencies (if not already done)
composer install

# 3. Start Docker containers
./vendor/bin/sail up -d

# 4. Run migrations and seed e-commerce data
./vendor/bin/sail artisan migrate:fresh --seed
```

This will create:
- 101 users
- 10 product categories
- 200 products
- 500 orders (6 months of history)
- 1,472 order items
- 207 invoices
- 101 payments
- **Total Revenue**: €929,574.57

### Testing the Setup

```bash
# List all available MCP tools
./vendor/bin/sail artisan mcp:list

# Test all MCP tools
./vendor/bin/sail php test_mcp_tools.php

# Start development environment (web server + queue + logs + Vite)
composer run dev
```

Your application will be available at:
- **Web**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080
- **Vite Dev Server**: http://localhost:5173

---

## Features

### MCP Tools (15)

#### User Management (3 tools)

| Tool | Description | Parameters |
|------|-------------|------------|
| **get_user_stats** | User statistics & distribution | None |
| **search_users** | Search users by name/email | `query` (string), `limit` (int) |
| **create_user** | Create new user accounts | `name`, `email`, `password` |

#### Sales Analytics (4 tools)

| Tool | Description | Parameters |
|------|-------------|------------|
| **get_sales_stats** | Comprehensive sales dashboard | None |
| **get_revenue_by_period** | Revenue breakdown by period | `period` (daily/weekly/monthly) |
| **get_top_products** | Best-selling products | `limit` (int), `by` (quantity/revenue) |
| **get_customer_insights** | Customer segments & LTV | None |

#### Inventory & Alerts (2 tools)

| Tool | Description | Parameters |
|------|-------------|------------|
| **get_inventory_alerts** | Low stock & overdue invoices | None |
| **get_product_inventory** | Product search with inventory | `query` (string), `limit` (int) |

#### Search & Details (2 tools)

| Tool | Description | Parameters |
|------|-------------|------------|
| **search_orders** | Flexible order filtering | `status`, `start_date`, `end_date`, `min_amount`, `customer_id`, `limit` |
| **get_invoice_details** | Invoice with payment tracking | `invoice_id` (int) |

#### System (3 tools)

| Tool | Description | Parameters |
|------|-------------|------------|
| **get_system_info** | Laravel/PHP/system details | None |
| **get_database_info** | Database tables & row counts | None |
| **health_check** | Application health monitoring | None |

### MCP Resources (2)

| Resource | URI | Description |
|----------|-----|-------------|
| **App Settings** | `config://app/settings` | Application configuration |
| **Laravel Info** | `system://laravel/info` | Laravel runtime information |

### E-commerce Database

Complete e-commerce schema with:

- **Users** (101) - Customer accounts
- **Categories** (10) - Product classifications (Electronics, Clothing, Books, etc.)
- **Products** (200) - Full inventory with pricing, stock, descriptions
- **Orders** (500) - 6 months of order history
- **Order Items** (1,472) - Line items with price snapshots
- **Invoices** (207) - Billing documents
- **Payments** (101) - Payment transactions

**Revenue Summary**: €929,574.57 across 6 months

---

## Tech Stack

| Component | Version | Purpose |
|-----------|---------|---------|
| **Laravel** | 12.36.1 | Application framework |
| **PHP** | 8.4.14 | Runtime |
| **MySQL** | 8.0.32 | Database |
| **MCP Package** | php-mcp/laravel v0.3.2 | MCP server implementation |
| **Docker** | Laravel Sail | Development environment |
| **Laravel Sanctum** | - | API authentication |
| **Laravel Breeze** | - | Auth scaffolding |

---

## Architecture

This project follows a **clean layered architecture** with clear separation of concerns:

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

### Architecture Principles

**1. Separation of Concerns**
- MCP Tools: Handle request/response only
- Services: Contain all business logic
- Models: Represent data and relationships

**2. Dependency Injection**
- Services injected into tools via constructor
- Laravel's service container manages dependencies
- Easy to test and maintain

**3. Single Responsibility**
- Each service has one clear purpose
- Services are grouped by domain (User, Analytics, etc.)
- Methods are focused and reusable

📖 **For detailed architecture documentation**, see [ARCHITECTURE.md](ARCHITECTURE.md)

---

## Configuration

### Claude Desktop Setup

Add to your Claude Desktop configuration:

**macOS**: `~/Library/Application Support/Claude/claude_desktop_config.json`
**Windows**: `%APPDATA%\Claude\claude_desktop_config.json`

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
        "mcp:serve",
        "--transport=stdio"
      ]
    }
  }
}
```

**Restart Claude Desktop** after adding this configuration.

### Docker Configuration

The application runs entirely in Docker using Laravel Sail. Services include:

- **laravel.test** - Main application container (PHP 8.4)
- **mysql** - MySQL 8.0 database
- **phpmyadmin** - Database management UI

### Environment Variables

Key configuration in `.env`:

```env
# Application
APP_PORT=8000
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
FORWARD_DB_PORT=3307

# Cache & Queue
CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_DRIVER=database

# phpMyAdmin
FORWARD_PHPMYADMIN_PORT=8080
```

---

## Usage

### Starting the Server

```bash
# Start all containers
./vendor/bin/sail up -d

# Start development environment (server + queue + logs + Vite)
composer run dev

# Start MCP server (STDIO for Claude Desktop)
./vendor/bin/sail artisan mcp:serve --transport=stdio

# Start MCP server (HTTP for web integrations)
./vendor/bin/sail artisan mcp:serve --transport=http
# Available at: http://localhost:8000/mcp
```

### MCP Commands

```bash
# Discover and register MCP tools
./vendor/bin/sail artisan mcp:discover

# List all MCP elements
./vendor/bin/sail artisan mcp:list

# List specific type
./vendor/bin/sail artisan mcp:list tools
./vendor/bin/sail artisan mcp:list resources

# JSON output
./vendor/bin/sail artisan mcp:list --json

# Test MCP tools
./vendor/bin/sail php test_mcp_tools.php
```

### Example Interactions

Once connected to Claude Desktop, try asking:

**Sales Analytics:**
- "What are the current sales statistics?"
- "Show me revenue trends for the last 6 months"
- "What are the top 10 products by revenue?"
- "Give me customer insights and segments"

**Inventory Management:**
- "What inventory alerts do we have?"
- "Show me products with low stock"
- "Search for products containing 'phone'"

**Order Management:**
- "Find all pending orders"
- "Show me orders from last month over €500"
- "What orders are from customer ID 5?"

**System Monitoring:**
- "Check application health"
- "What's the system information?"
- "Show me all database tables and row counts"

**User Management:**
- "How many users do we have?"
- "Search for users with email containing 'gmail'"
- "Create a new user named John Doe"

---

## Development

### Project Structure

```
laravel-mcp-server/
├── app/
│   ├── Http/Controllers/
│   ├── Models/              # User, Product, Order, etc.
│   ├── Mcp/
│   │   └── Tools/          # MCP Tool classes
│   └── Services/
│       └── LaravelMcpService.php  # Main MCP service
├── bootstrap/
│   └── app.php             # Application bootstrap
├── config/
│   └── mcp.php             # MCP configuration
├── database/
│   ├── factories/          # Model factories
│   ├── migrations/         # Database migrations
│   └── seeders/            # DatabaseSeeder
├── docker/
│   ├── 8.4/                # PHP 8.4 Dockerfile
│   └── mysql/
├── routes/
│   ├── api.php             # API routes
│   ├── mcp.php             # MCP routes
│   └── web.php             # Web routes
├── tests/
│   ├── Feature/
│   └── Unit/
├── compose.yaml            # Docker Compose config
├── test_mcp_tools.php      # MCP testing script
└── README.md               # This file
```

### Adding New MCP Tools

1. **Create a tool class** in `app/Mcp/Tools/`:

```php
<?php

namespace App\Mcp\Tools;

use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class YourToolName extends Tool
{
    protected string $description = 'Your tool description';

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'param1' => 'required|string',
        ]);

        // Your logic here
        $data = [
            'result' => 'value',
        ];

        return Response::text(json_encode($data, JSON_PRETTY_PRINT));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'param1' => $schema->string()
                ->description('Parameter description')
                ->required(),
        ];
    }
}
```

2. **Discover the new tool**:

```bash
./vendor/bin/sail artisan mcp:discover
./vendor/bin/sail artisan mcp:list
```

### Adding New Resources

Add methods to `LaravelMcpService.php` with the `#[McpResource]` attribute:

```php
use PhpMcp\Server\Attributes\McpResource;

#[McpResource(
    uri: 'your://resource/uri',
    mimeType: 'application/json'
)]
public function yourResource(): array
{
    return [
        'data' => 'value',
    ];
}
```

### Common Commands

#### Testing

```bash
# Run all tests
./vendor/bin/sail test
# OR
composer run test

# Run specific test
./vendor/bin/sail artisan test tests/Feature/ExampleTest.php

# Run with coverage
./vendor/bin/sail artisan test --coverage
```

#### Code Quality

```bash
# Format code with Laravel Pint
./vendor/bin/sail pint

# Check code without fixing
./vendor/bin/sail pint --test
```

#### Database

```bash
# Run migrations
./vendor/bin/sail artisan migrate

# Fresh migration with seeders
./vendor/bin/sail artisan migrate:fresh --seed

# Create new migration
./vendor/bin/sail artisan make:migration create_example_table

# Check database status
./vendor/bin/sail artisan db:show

# Access MySQL CLI
./vendor/bin/sail mysql
```

#### Artisan

```bash
# Laravel REPL
./vendor/bin/sail artisan tinker

# Create model with migration and factory
./vendor/bin/sail artisan make:model Example -mf

# Create controller
./vendor/bin/sail artisan make:controller ExampleController

# Clear caches
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan view:clear
```

#### Composer & NPM

```bash
# PHP dependencies
./vendor/bin/sail composer install
./vendor/bin/sail composer require package/name

# Node dependencies
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev    # Development build
./vendor/bin/sail npm run build  # Production build
```

---

## Database

### Schema Overview

**Core Tables:**

| Table | Rows | Purpose |
|-------|------|---------|
| users | 101 | Customer accounts |
| categories | 10 | Product categories |
| products | 200 | Product catalog |
| orders | 500 | Customer orders |
| order_items | 1,472 | Order line items |
| invoices | 207 | Billing documents |
| payments | 101 | Payment transactions |

**Relationships:**
- Users → Orders (1:n)
- Orders → Order Items (1:n)
- Products → Order Items (1:n)
- Categories → Products (1:n)
- Orders → Invoices (1:n)
- Invoices → Payments (1:n)

### External Access

Connect to the database using your favorite client:

| Parameter | Value |
|-----------|-------|
| Host | `localhost` |
| Port | `3307` |
| Database | `laravel` |
| Username | `sail` |
| Password | `password` |

### Migrations & Seeding

```bash
# Run migrations
./vendor/bin/sail artisan migrate

# Rollback last migration
./vendor/bin/sail artisan migrate:rollback

# Fresh migration with seeding
./vendor/bin/sail artisan migrate:fresh --seed

# Seed only
./vendor/bin/sail artisan db:seed
```

---

## Testing

### MCP Tools Testing

```bash
# Test all MCP tools
./vendor/bin/sail php test_mcp_tools.php

# List available tools
./vendor/bin/sail artisan mcp:list

# Test in Tinker
./vendor/bin/sail artisan tinker
>>> $service = new App\Services\LaravelMcpService();
>>> $service->getSalesStats();
>>> $service->getInventoryAlerts();
```

### Laravel Tests

```bash
# Run all tests
./vendor/bin/sail test

# Run specific test file
./vendor/bin/sail artisan test tests/Feature/Auth/AuthenticationTest.php

# Run with coverage
./vendor/bin/sail artisan test --coverage
```

---

## API & Authentication

### API Routes

Base URL: `http://localhost:8000/api/`

**Authentication endpoints** (Laravel Breeze):
- `POST /api/register` - User registration
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `GET /api/user` - Get authenticated user (protected)

### Testing API

```bash
# Login and get token
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Use token to access protected route
curl http://localhost:8000/api/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## Troubleshooting

### Containers Won't Start

```bash
./vendor/bin/sail down
docker system prune -f
./vendor/bin/sail up -d
```

### Permission Errors

```bash
./vendor/bin/sail artisan storage:link
sudo chown -R $USER:$USER storage bootstrap/cache
```

### Tools Not Discovered

```bash
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan mcp:discover
./vendor/bin/sail artisan mcp:list
```

### Clear All Caches

```bash
./vendor/bin/sail artisan optimize:clear
```

### CSRF Token Errors

The MCP HTTP transport uses `api` middleware which doesn't require CSRF tokens by default. If you encounter CSRF issues, check `config/mcp.php`.

### Database Connection Issues

```bash
# Check if MySQL is running
./vendor/bin/sail ps

# View MySQL logs
./vendor/bin/sail logs mysql

# Test connection
./vendor/bin/sail mysql -u sail -p
```

---

## Production Deployment

### Process Supervisor

Use Supervisor to keep the MCP server running:

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

### Environment Configuration

```bash
# Set production environment
APP_ENV=production
APP_DEBUG=false

# Use production cache
CACHE_STORE=redis
QUEUE_CONNECTION=redis

# Secure session
SESSION_SECURE_COOKIE=true
```

### Optimization

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

---

## Security Considerations

- MCP tools have **direct database access** - implement authorization in production
- Validate all inputs using `#[Schema]` attributes
- Use environment-specific configurations
- Consider **rate limiting** for HTTP transport
- Never commit `.env` file - contains sensitive credentials
- Enable **HTTPS** in production
- Implement **API authentication** for MCP HTTP endpoints
- Review and audit tool permissions regularly

---

## Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests: `./vendor/bin/sail test`
5. Format code: `./vendor/bin/sail pint`
6. Submit a pull request

---

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## Resources & Documentation

- **php-mcp/laravel**: [github.com/php-mcp/laravel](https://github.com/php-mcp/laravel)
- **MCP Specification**: [modelcontextprotocol.io](https://modelcontextprotocol.io)
- **Laravel Documentation**: [laravel.com/docs](https://laravel.com/docs)
- **Laravel Sail**: [laravel.com/docs/sail](https://laravel.com/docs/sail)

---

**Built with Laravel 12, PHP 8.4, and php-mcp/laravel**
**Created: November 2025**
