# Laravel MCP Server

### Production-Ready AI Integration for Business Intelligence

**A comprehensive Model Context Protocol (MCP) server implementation that bridges AI assistants with enterprise business systems, demonstrating how natural language interfaces can transform business intelligence and data access.**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com)
[![Docker](https://img.shields.io/badge/Docker-Sail-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://laravel.com/docs/sail)
[![MCP](https://img.shields.io/badge/MCP-Protocol-5C2D91?style=for-the-badge)](https://modelcontextprotocol.io)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](https://opensource.org/licenses/MIT)

[Business Value](#business-value) • [Technical Overview](#technical-overview) • [Features](#features) • [Quick Start](#quick-start) • [Documentation](#documentation)

---

## Table of Contents

- [Business Value](#business-value)
  - [The Problem](#the-problem)
  - [The Solution](#the-solution)
  - [What This Architecture Brings](#what-this-architecture-brings)
  - [Real-World Impact](#real-world-impact)
- [Technical Overview](#technical-overview)
  - [What is MCP?](#what-is-mcp)
  - [Architecture Highlights](#architecture-highlights)
  - [Tech Stack](#tech-stack)
- [Features](#features)
  - [MCP Tools (23 Tools)](#mcp-tools-23-tools)
  - [MCP Resources](#mcp-resources)
  - [Sample Dataset](#sample-dataset)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [Usage Examples](#usage-examples)
- [Development Guide](#development-guide)
- [Testing](#testing)
- [Deployment](#deployment)
- [Documentation](#documentation)

---

## Business Value

### The Problem

Modern businesses face significant challenges in accessing and analyzing their data:

- **Data Silos**: Critical business information locked in complex databases requiring SQL expertise
- **Slow Insights**: Hours or days of waiting for custom reports from specialized data teams
- **High Costs**: Expensive BI tools with steep learning curves and per-seat licensing
- **Static Reporting**: Traditional dashboards can't answer follow-up questions or adapt to context
- **Limited Accessibility**: Only data analysts and technical users can extract meaningful insights

**The Bottom Line**: Decision-makers need answers in seconds, not days. Business intelligence should be conversational, not technical.

### The Solution

This project implements the **Model Context Protocol (MCP)**—an emerging open standard developed by Anthropic—to create secure, structured bridges between AI assistants and business systems.

Think of it as **"REST APIs for AI"**: instead of building rigid dashboards or waiting for analyst reports, stakeholders can have natural conversations with their data through AI assistants like Claude.

**Example Interaction:**
```
User: "What were our top 5 products last month by revenue?"
Claude: [Instantly queries the database through MCP and returns precise results]

User: "Show me customer purchase patterns for our VIP segment"
Claude: [Performs RFM analysis and provides segmentation insights]

User: "Create an order for customer #42 with 3 units of product #15"
Claude: [Validates stock, creates order, updates inventory, returns confirmation]
```

### What This Architecture Brings

#### 1. **Democratized Data Access**

Traditional BI systems require training, specialized skills, and often expensive licenses. MCP-powered AI access transforms data into a conversational interface accessible to anyone in the organization.

**Business Impact:**
- **Universal Access**: Every employee can query business data in natural language
- **Zero Training Required**: No SQL, no BI tool training, no technical prerequisites
- **Contextual Intelligence**: AI understands business context and provides relevant insights
- **Role-Based Insights**: Same data, different perspectives based on user needs

**ROI Example**: A mid-size e-commerce company (€10M revenue, 100 employees) can save approximately **€299,000 annually** by eliminating BI software licenses, reducing analyst workload, and accelerating decision-making. ([See full business case](BUSINESS_CASE.md))

#### 2. **Real-Time Decision Support**

Unlike static dashboards updated daily or weekly, MCP provides instant access to live data with the intelligence to interpret it.

**Business Impact:**
- **Speed**: 99.9% faster than traditional BI workflows (seconds vs hours)
- **Agility**: Respond to market changes and operational issues immediately
- **Proactive Intelligence**: AI can identify patterns, anomalies, and opportunities
- **Executive Empowerment**: C-suite can make data-driven decisions without intermediaries

**Use Case**: A CEO preparing for a board meeting can ask "Compare Q4 revenue to last year, identify growth drivers, and flag any concerning trends" and receive a comprehensive analysis in under 30 seconds.

#### 3. **Unified Business Operations**

MCP servers can orchestrate multiple business systems, providing a single conversational interface for complex operations spanning CRM, ERP, inventory, and analytics.

**Business Impact:**
- **Operational Efficiency**: Reduce context switching between multiple systems
- **Automated Workflows**: AI can execute multi-step processes through natural language commands
- **Consistency**: Single source of truth with unified business rules
- **Integration Without Migration**: Connect existing systems without expensive re-platforming

**Use Case**: Customer service agents can access order history, customer lifetime value, inventory status, and create support tickets—all through conversational AI instead of navigating 4+ different systems.

#### 4. **Innovation Platform**

Beyond immediate operational benefits, MCP architecture positions organizations at the forefront of AI-enterprise integration, enabling future innovations impossible with traditional systems.

**Research & Innovation Opportunities:**
- **Autonomous Business Processes**: AI agents that can independently manage routine operations
- **Predictive Analytics**: Real-time forecasting and anomaly detection
- **Natural Language Database Design**: Describe business needs, AI generates schema
- **Cross-System Intelligence**: AI that understands relationships across all business domains

**Strategic Value**: Early adopters establish competitive advantages in:
- **Talent Attraction**: Engineers want to work with cutting-edge AI technologies
- **Customer Experience**: Superior service through AI-powered personalization
- **Market Positioning**: Thought leadership in AI-driven business transformation
- **Vendor Independence**: Open standards prevent lock-in to proprietary platforms

### Real-World Impact

#### Time Savings
| Task | Traditional Approach | MCP-Enabled | Improvement |
|------|---------------------|-------------|-------------|
| Generate sales report | 2-4 hours | 10 seconds | **99.9% faster** |
| Customer segmentation analysis | 1-2 days | 15 seconds | **99.99% faster** |
| Inventory stock alert | Manual CSV export + analysis | Real-time query | **Instant** |
| Multi-system data correlation | 4-8 hours (multiple teams) | 30 seconds | **1000x faster** |

#### Cost Reduction
| Expense | Annual Cost (Traditional) | MCP Architecture | Savings |
|---------|--------------------------|------------------|---------|
| BI Software Licenses | €45,000 | €0 (open source) | **€45,000** |
| Data Analyst Time (Ad-hoc reports) | €90,000 | €18,000 (80% reduction) | **€72,000** |
| Report Development | €50,000 | €5,000 | **€45,000** |
| Training & Onboarding | €19,500 | €2,000 | **€17,500** |
| **Total Annual Savings** | | | **€179,500** |

*Based on mid-size e-commerce company (€10M revenue, 100 employees). [Full ROI analysis](BUSINESS_CASE.md)*

#### Accessibility Improvement
- **Traditional BI**: 5-10% of employees (data analysts, BI specialists)
- **MCP-Enabled**: 100% of employees with AI assistant access
- **Result**: **10-20x democratization** of business intelligence

---

## Technical Overview

### What is MCP?

The **Model Context Protocol (MCP)** is an open standard developed by Anthropic for connecting AI assistants to external systems through structured, secure interfaces. It defines how AI models can discover, invoke, and interact with tools and data sources.

**Key Concepts:**

- **Tools**: Callable functions that AI can invoke (e.g., `get_sales_stats`, `create_order`)
- **Resources**: Data endpoints that AI can read (e.g., configuration files, system information)
- **Prompts**: Reusable templates for common workflows
- **Transport**: Communication channels (STDIO for local, HTTP for remote)

**Why MCP Matters:**

1. **Standardization**: Universal protocol works with any MCP-compatible AI assistant
2. **Security**: Structured authorization and validation at the protocol level
3. **Discoverability**: AI automatically understands available capabilities through schemas
4. **Vendor Independence**: Not tied to a specific AI model or cloud provider
5. **Type Safety**: Strong typing prevents errors and improves reliability

### Architecture Highlights

This implementation demonstrates production-ready patterns for enterprise MCP servers:

```
┌─────────────────────────────────────────────┐
│        AI Assistant (Claude Desktop)         │
│          Natural Language Interface          │
└──────────────────┬──────────────────────────┘
                   │ MCP Protocol (STDIO/HTTP)
┌──────────────────▼──────────────────────────┐
│         Laravel MCP Server Layer            │
│  ┌─────────────────────────────────────┐   │
│  │    MCP Tools (23 tools)             │   │
│  │  • Request validation               │   │
│  │  • Schema definition                │   │
│  │  • Response formatting              │   │
│  └──────────────┬──────────────────────┘   │
│                 │ Dependency Injection       │
│  ┌──────────────▼──────────────────────┐   │
│  │    Business Logic Services (13)     │   │
│  │  • SalesAnalyticsService            │   │
│  │  • CustomerInsightsService          │   │
│  │  • RfmAnalysisService               │   │
│  │  • SalesForecastService             │   │
│  │  • InventoryManagementService       │   │
│  │  • OrderCreationService             │   │
│  │  • UserService, InvoiceService...   │   │
│  └──────────────┬──────────────────────┘   │
│                 │                            │
│  ┌──────────────▼──────────────────────┐   │
│  │    Data Access Layer                │   │
│  │  • Eloquent Models (7 models)       │   │
│  │  • Relationships & Query Builders   │   │
│  └──────────────┬──────────────────────┘   │
│                 │                            │
│  ┌──────────────▼──────────────────────┐   │
│  │    Infrastructure                   │   │
│  │  • MySQL Database (12 tables)       │   │
│  │  • Cache (Database driver)          │   │
│  │  • Queue (Database connection)      │   │
│  └─────────────────────────────────────┘   │
└─────────────────────────────────────────────┘
```

**Architectural Principles:**

1. **Clean Architecture**: Separation of concerns across distinct layers
   - MCP Tools: Protocol layer (thin controllers)
   - Services: Business logic (framework-agnostic)
   - Models: Data persistence (Eloquent ORM)

2. **Dependency Injection**: All services injected via constructor
   - Promotes testability and maintainability
   - Loose coupling between components
   - Laravel container handles resolution automatically

3. **Domain-Driven Design**: Services organized by business domain
   - Analytics (sales, customers, forecasting, RFM)
   - Inventory (stock management, product catalog)
   - Orders (creation, search, status updates)
   - Users (account management, statistics)
   - System (health monitoring, diagnostics)

4. **Read + Write Capabilities**: Beyond traditional BI (read-only)
   - Create orders, products, and users
   - Update inventory and pricing
   - Manage order statuses and workflows
   - Execute complex business operations

5. **Type Safety & Validation**: Schema-driven development
   - All tool inputs validated via MCP schemas
   - Strong typing throughout (PHP 8.4 features)
   - Response standardization

**For detailed architecture documentation, see [ARCHITECTURE.md](ARCHITECTURE.md)**

### Tech Stack

| Technology | Version | Purpose |
|-----------|---------|---------|
| **Laravel** | 12.x | Application framework |
| **PHP** | 8.4 | Runtime (constructor property promotion, typed properties) |
| **MySQL** | 8.0 | Relational database |
| **Laravel MCP** | 0.3.2 | MCP protocol implementation |
| **Laravel Sail** | 1.47+ | Docker development environment |
| **Laravel Sanctum** | 4.0+ | API authentication |
| **Laravel Breeze** | 2.3+ | Authentication scaffolding |
| **Docker Compose** | - | Container orchestration |
| **phpMyAdmin** | latest | Database management interface |

**Development Tools:**
- **Laravel Pint**: Code style and formatting (PSR-12)
- **PHPUnit**: Unit and feature testing (11.5.3)
- **Laravel Tinker**: Interactive REPL
- **Laravel Pail**: Real-time log viewing

---

## Features

### MCP Tools (23 Tools)

This server provides 23 production-ready MCP tools organized by business domain:

#### Advanced Analytics (2 Tools)

| Tool | Description | Key Features |
|------|-------------|--------------|
| `get_rfm_analysis` | Customer segmentation via RFM (Recency, Frequency, Monetary) analysis | 9 customer segments (Champions, Loyal, At Risk, etc.), quartile-based scoring (1-5), actionable insights per segment |
| `get_sales_forecast` | Predictive sales forecasting with trend analysis | Moving average algorithms, confidence metrics, seasonality detection, configurable periods (daily/weekly/monthly) |

#### Product Intelligence (3 Tools)

| Tool | Description | Key Features |
|------|-------------|--------------|
| `get_product_recommendations` | AI-powered product recommendations | Collaborative filtering, cross-sell suggestions, upsell opportunities |
| `get_trending_products` | Identify trending products with velocity metrics | Sales momentum tracking, growth rate analysis |
| `get_top_products` | Best-selling products analysis | Sortable by revenue or quantity, configurable limits |

#### User Management (3 Tools)

| Tool | Description | Key Features |
|------|-------------|--------------|
| `get_user_stats` | User statistics and distribution | Total users, registration trends, role distribution |
| `search_users` | Search users by name or email | Flexible filtering, pagination support |
| `create_user` | Create new user accounts | Validation, password hashing, role assignment |

#### Sales Analytics (3 Tools)

| Tool | Description | Key Features |
|------|-------------|--------------|
| `get_sales_stats` | Comprehensive sales dashboard with KPIs | Total orders, revenue, AOV, payment methods, growth rates (today, this week, this month) |
| `get_revenue_by_period` | Revenue breakdown by time period | Daily/weekly/monthly aggregation, trend analysis |
| `get_customer_insights` | Customer analytics and lifetime value | Segmentation (VIP, Loyal, At Risk, One-Time, Regular), LTV calculation, purchase behavior |

#### Inventory Management (5 Tools)

| Tool | Description | Key Features |
|------|-------------|--------------|
| `get_inventory_alerts` | Stock alerts and warnings | Low stock, out of stock, overdue invoices |
| `get_product_inventory` | Product search with stock levels | Flexible filtering, stock status, pricing |
| `update_product_stock` | Update product stock levels | Operations: set, add, subtract; validation, stock status calculation |
| `create_product` | Create new products | Auto SKU generation, validation, category assignment |
| `apply_discount` | Dynamic pricing - apply discounts | Percentage-based discounts, price validation |

#### Order Management (4 Tools)

| Tool | Description | Key Features |
|------|-------------|--------------|
| `search_orders` | Advanced order filtering and search | Filter by status, date range, amount range, customer ID; pagination |
| `create_order` | Create new orders | Stock validation, tax calculation (20%), automatic inventory updates, shipping calculation |
| `update_order_status` | Update order status | Status transitions (pending → processing → shipped → delivered), timestamp tracking |
| `get_invoice_details` | Detailed invoice information | Invoice data, payment tracking, line items, due dates |

#### System Monitoring (3 Tools)

| Tool | Description | Key Features |
|------|-------------|--------------|
| `get_system_info` | Laravel/PHP/Server information | Framework version, PHP version, environment, server details |
| `get_database_info` | Database statistics and table information | Table counts, row counts, database size, schema information |
| `health_check` | Application health monitoring | Database connectivity, cache status, storage permissions, overall health |

### MCP Resources

| Resource | URI | Description |
|----------|-----|-------------|
| **App Settings** | `config://app/settings` | Application configuration snapshot (name, environment, debug status, URL, timezone, locale) |
| **Laravel Info** | `system://laravel/info` | Framework runtime information (version, environment, cache configuration) |

### Sample Dataset

The project includes comprehensive sample data for realistic demonstrations:

```
Users:        101 (100 customers + 1 admin)
Categories:   10
Products:     200
Orders:       500
Order Items:  ~1,500-2,500
Invoices:     ~350 (70% of orders)
Payments:     ~350 (matches paid invoices)

Total Revenue:     ~€930,000
Historical Range:  6 months
Order Statuses:    Pending, Processing, Shipped, Delivered, Cancelled
Payment Methods:   Credit Card, Debit Card, PayPal, Bank Transfer
```

**Sample Data Highlights:**
- Realistic pricing: Products from €10 to €2,000
- Variable order sizes: 1-5 items per order, 1-3 units per item
- Tax calculation: 20% on all orders
- Shipping costs: €5-€25 per order
- Low stock scenarios: Some products critically low for testing alerts
- Overdue invoices: Demonstrates payment tracking capabilities

---

## Quick Start

### Prerequisites

- **Docker Desktop** - [Download](https://www.docker.com/products/docker-desktop)
- **Composer** - [Install guide](https://getcomposer.org/download/)
- **MCP-Compatible AI Client** - [Claude Desktop](https://claude.ai/download) (recommended), Cursor, or [other clients](https://modelcontextprotocol.io/clients)

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/yourusername/laravel-mcp-server.git
cd laravel-mcp-server

# 2. Install PHP dependencies
composer install

# 3. Configure environment
cp .env.example .env

# 4. Build and start Docker containers
./vendor/bin/sail build --no-cache
./vendor/bin/sail up -d

# 5. Generate application key
./vendor/bin/sail artisan key:generate

# 6. Initialize database with sample data
./vendor/bin/sail artisan migrate:fresh --seed
```

### Verify Installation

```bash
# Check container status
./vendor/bin/sail ps

# Expected output:
# NAME                                    STATE    PORTS
# laravel-mcp-server-laravel.test-1       running  0.0.0.0:80->80/tcp, 0.0.0.0:5173->5173/tcp
# laravel-mcp-server-mysql-1              running  0.0.0.0:3307->3306/tcp
# laravel-phpmyadmin                      running  0.0.0.0:8080->80/tcp
```

**Access Points:**

- **Application**: http://localhost
- **phpMyAdmin**: http://localhost:8080 (user: `sail`, password: `password`)
- **Database**: localhost:3307 (from host machine)

### Configure Claude Desktop

Add the following to your Claude Desktop configuration file:

**Configuration File Locations:**
- **macOS**: `~/Library/Application Support/Claude/claude_desktop_config.json`
- **Windows**: `%APPDATA%\Claude\claude_desktop_config.json`
- **Linux**: `~/.config/Claude/claude_desktop_config.json`

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

**Important Notes:**
- Verify the container name matches your running container: `docker ps`
- Restart Claude Desktop completely after adding this configuration
- The container must be running (`./vendor/bin/sail up -d`) before starting Claude Desktop

### Test the Integration

1. **Open Claude Desktop** (must be restarted after configuration)
2. **Verify MCP Connection**: Look for the MCP server indicator in the Claude Desktop interface
3. **Try Example Queries**:
   ```
   "What are my total sales statistics?"
   "Show me products that are low on stock"
   "What were the top 5 products last month by revenue?"
   "Perform RFM analysis on customers"
   ```

---

## Configuration

### Environment Configuration

Key settings in `.env`:

```env
# Application
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=mysql                  # Container name in Docker network
DB_PORT=3306                   # Internal port (use 3307 from host)
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

# External Access (from host machine)
FORWARD_DB_PORT=3307           # Avoids conflict with local MySQL
FORWARD_PHPMYADMIN_PORT=8080

# Performance (database-backed for development)
CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_DRIVER=database
```

### Docker Services

| Service | Internal Port | External Port | Purpose |
|---------|--------------|---------------|---------|
| laravel.test | 80 | 80 | Main Laravel application |
| laravel.test | 5173 | 5173 | Vite development server |
| mysql | 3306 | 3307 | MySQL database server |
| phpmyadmin | 80 | 8080 | Database management UI |

### MCP Server Configuration

The MCP server is registered in `routes/ai.php`:

```php
use App\Mcp\Servers\LaravelServer;
use Laravel\Mcp\Facades\Mcp;

// STDIO-based MCP server (for Claude Desktop)
// Mcp::local('laravel', LaravelServer::class);

// HTTP-based MCP server (for web clients)
Mcp::web('/mcp/laravel', LaravelServer::class);
```

**Transport Options:**
- **STDIO** (`Mcp::local`): For local AI clients like Claude Desktop (uses stdin/stdout)
- **HTTP** (`Mcp::web`): For remote AI clients or web-based integrations

**Current Configuration**: HTTP transport at endpoint `/mcp/laravel` (exposed on port 80)

---

## Usage Examples

### Natural Language Queries

Once connected to Claude Desktop, interact using natural language:

#### Sales & Revenue Analysis

```
"What are my total sales for this quarter?"
"Show me the top 5 products by revenue"
"What's the revenue breakdown for the last 30 days by week?"
"How are sales trending month over month?"
"Give me a complete sales dashboard with all key metrics"
```

#### Customer Intelligence

```
"Perform RFM analysis on my customer base"
"Show me customer insights and segmentation"
"Who are my VIP customers?"
"Which customers are at risk of churning?"
"What's the average customer lifetime value?"
```

#### Inventory Management

```
"What products are low on stock?"
"Show me all products that are out of stock"
"Search for products containing 'laptop'"
"Update stock for product #15 by adding 50 units"
"Create a new product: Gaming Mouse, SKU GM001, price €49.99, stock 100"
"Apply a 15% discount to product #23"
```

#### Order Operations

```
"Find all pending orders"
"Show orders from the last 30 days over €1000"
"Create an order for customer #42 with 3 units of product #15"
"Update order #123 status to shipped"
"Search for orders by customer ID 42"
```

#### Financial Analysis

```
"Get invoice details for invoice #42"
"Show me all overdue invoices"
"What's the total value of unpaid invoices?"
"Give me payment method distribution for this month"
```

#### Predictive Analytics

```
"Forecast sales for the next 14 days"
"What products are trending right now?"
"Give me product recommendations for customer #25"
```

#### System Monitoring

```
"Check application health status"
"Show me system information"
"Get database statistics and table counts"
"What's the database size and row counts?"
```

### Multi-Step Workflows

The AI can execute complex workflows by chaining multiple tools:

```
"Analyze our top 10 customers by spending, check their order frequency,
and identify which ones haven't ordered in over 90 days"

→ AI executes: get_customer_insights + search_orders (filtered by customer) + RFM analysis
```

```
"Find products with less than 10 units in stock, check if they're among
our top sellers, and recommend which ones to restock first"

→ AI executes: get_inventory_alerts + get_top_products + correlation analysis
```

---

## Development Guide

### Project Structure

```
laravel-mcp-server/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # HTTP controllers
│   │   ├── Middleware/           # HTTP middleware
│   │   └── Requests/             # Form request validation
│   ├── Models/                   # Eloquent models (7 models)
│   │   ├── User.php
│   │   ├── Category.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── Invoice.php
│   │   └── Payment.php
│   ├── Mcp/
│   │   ├── Tools/                # 23 MCP tool implementations
│   │   ├── Resources/            # 2 MCP resources
│   │   └── Servers/
│   │       └── LaravelServer.php # MCP server registration
│   ├── Services/                 # Business logic layer (13 services)
│   │   ├── Analytics/            # Sales, customers, RFM, forecasting
│   │   ├── Inventory/            # Stock management, product catalog
│   │   ├── Invoice/              # Invoice operations
│   │   ├── Order/                # Order processing
│   │   ├── System/               # Health monitoring
│   │   └── User/                 # User management
│   └── Providers/                # Service providers
├── database/
│   ├── factories/                # Model factories for testing
│   ├── migrations/               # 12 database migrations
│   └── seeders/
│       └── DatabaseSeeder.php    # Sample data generation
├── docker/                       # Docker configuration
│   ├── Dockerfile                # PHP 8.4 Alpine image
│   └── mysql/                    # MySQL initialization scripts
├── routes/
│   ├── ai.php                    # MCP server routes
│   ├── api.php                   # API endpoints
│   ├── web.php                   # Web routes
│   └── auth.php                  # Authentication routes
├── tests/
│   ├── Feature/                  # Integration tests
│   └── Unit/                     # Unit tests
├── compose.yaml                  # Docker Compose configuration
├── phpunit.xml                   # PHPUnit configuration
└── Documentation (7 files)
    ├── README.md                 # This file
    ├── ARCHITECTURE.md           # Detailed architecture documentation
    ├── BUSINESS_CASE.md          # ROI analysis and business justification
    ├── DEMO_SCENARIOS.md         # Demo scripts for various audiences
    ├── DEMO_PROMPTS.md           # Ready-to-use demo prompts (French)
    ├── PITCH_GUIDE.md            # Sales enablement material
    └── DOCUMENTATION_INDEX.md    # Navigation guide
```

### Creating New MCP Tools

Follow these steps to add new business capabilities:

#### 1. Generate Tool Class

```bash
./vendor/bin/sail artisan make:mcp-tool GetProductAnalytics
```

#### 2. Implement Tool Logic

```php
<?php

namespace App\Mcp\Tools;

use App\Services\Analytics\ProductAnalyticsService;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetProductAnalytics extends Tool
{
    protected string $description = 'Get comprehensive product performance analytics';

    public function __construct(
        private readonly ProductAnalyticsService $analyticsService
    ) {}

    public function handle(Request $request): Response
    {
        $productId = $request->input('product_id');
        $period = $request->input('period', 'monthly');

        $analytics = $this->analyticsService->getProductAnalytics($productId, $period);

        return Response::text(json_encode($analytics, JSON_PRETTY_PRINT));
    }

    public function schema(): array
    {
        return [
            'product_id' => [
                'type' => 'integer',
                'description' => 'Product ID to analyze',
                'required' => true,
            ],
            'period' => [
                'type' => 'string',
                'description' => 'Analysis period (daily, weekly, monthly)',
                'enum' => ['daily', 'weekly', 'monthly'],
                'required' => false,
            ],
        ];
    }
}
```

#### 3. Create Service (if needed)

```bash
./vendor/bin/sail artisan make:service Analytics/ProductAnalyticsService
```

Implement service logic following the clean architecture pattern:

```php
<?php

namespace App\Services\Analytics;

use App\Models\Product;
use App\Models\OrderItem;

class ProductAnalyticsService
{
    public function getProductAnalytics(int $productId, string $period): array
    {
        $product = Product::findOrFail($productId);

        // Business logic here (no MCP/HTTP dependencies)
        // Return plain arrays for maximum reusability

        return [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
            ],
            'sales_data' => $this->calculateSalesData($product, $period),
            'performance_metrics' => $this->calculatePerformanceMetrics($product),
            'timestamp' => now()->toIso8601String(),
        ];
    }

    private function calculateSalesData(Product $product, string $period): array
    {
        // Implementation...
    }

    private function calculatePerformanceMetrics(Product $product): array
    {
        // Implementation...
    }
}
```

#### 4. Register Tool

Edit `app/Mcp/Servers/LaravelServer.php`:

```php
protected array $tools = [
    // ... existing tools

    // Product Analytics
    GetProductAnalytics::class,
];
```

#### 5. Test Your Tool

**Option 1: Manual Testing (Host Machine)**

Since the MCP Inspector cannot run inside the Docker container, install and run it from your host machine:

```bash
# On your host machine (not inside the container)
npx @modelcontextprotocol/inspector http://localhost/mcp/laravel
```

This will:
- Start the MCP Inspector on http://localhost:5173
- Connect to your Dockerized Laravel app at http://localhost/mcp/laravel
- Allow you to test tools, view schemas, and debug requests

**Option 2: Laravel Tinker**

```bash
./vendor/bin/sail artisan tinker

>>> $service = app(\App\Services\Analytics\ProductAnalyticsService::class);
>>> $service->getProductAnalytics(1, 'monthly');
```

**Option 3: Claude Desktop**

After registering the tool, restart Claude Desktop and test with natural language:
```
"Get product analytics for product #1 with monthly period"
```

### Service Layer Pattern

**Key Principles:**

1. **Framework Agnostic**: Services should have no dependencies on HTTP, MCP, or Laravel-specific features
2. **Return Arrays**: Use plain arrays instead of Response objects for maximum reusability
3. **Single Responsibility**: Each service focuses on one business domain
4. **Dependency Injection**: Use constructor injection for all dependencies
5. **Type Safety**: Leverage PHP 8.4 typed properties and return types

**Example Service Structure:**

```php
<?php

namespace App\Services\Domain;

use App\Models\Model;

class DomainService
{
    public function __construct(
        private readonly RelatedService $relatedService,
        private readonly AnotherDependency $dependency
    ) {}

    public function businessOperation(int $id, array $options): array
    {
        // 1. Validate input (or use validators)
        // 2. Fetch data (using Eloquent)
        // 3. Apply business logic
        // 4. Return structured array

        return [
            'data' => [...],
            'metadata' => [...],
            'timestamp' => now()->toIso8601String(),
        ];
    }

    private function helperMethod(): void
    {
        // Private helpers for complex logic
    }
}
```

### Common Development Commands

#### Application Management

```bash
# Start containers
./vendor/bin/sail up -d

# Stop containers
./vendor/bin/sail down

# Restart containers
./vendor/bin/sail restart

# View logs (follow mode)
./vendor/bin/sail logs -f

# View logs for specific service
./vendor/bin/sail logs -f laravel.test
./vendor/bin/sail logs -f mysql

# Access container shell
./vendor/bin/sail shell

# Execute artisan commands
./vendor/bin/sail artisan <command>
```

#### Database Operations

```bash
# Run migrations
./vendor/bin/sail artisan migrate

# Rollback migrations
./vendor/bin/sail artisan migrate:rollback

# Fresh migration with sample data
./vendor/bin/sail artisan migrate:fresh --seed

# Access MySQL CLI
./vendor/bin/sail mysql

# Database status
./vendor/bin/sail artisan db:show

# Database table information
./vendor/bin/sail artisan db:table users
```

#### Code Quality & Testing

```bash
# Run all tests
./vendor/bin/sail test

# Run specific test file
./vendor/bin/sail artisan test tests/Feature/Auth/AuthenticationTest.php

# Run tests with coverage
./vendor/bin/sail artisan test --coverage

# Code formatting (Laravel Pint)
./vendor/bin/sail pint

# Code formatting (check only, no changes)
./vendor/bin/sail pint --test

# Interactive REPL
./vendor/bin/sail artisan tinker
```

#### Cache Management

```bash
# Clear all caches
./vendor/bin/sail artisan optimize:clear

# Individual cache clearing
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan view:clear
./vendor/bin/sail artisan cache:clear

# Cache configuration (production)
./vendor/bin/sail artisan config:cache
./vendor/bin/sail artisan route:cache
./vendor/bin/sail artisan view:cache
```

---

## Testing

### MCP Inspector (Recommended Method)

The MCP Inspector provides an interactive web interface for testing MCP tools. Due to Docker networking, it must be run from the host machine:

```bash
# On your host machine (not inside the Docker container)
npx @modelcontextprotocol/inspector http://localhost/mcp/laravel
```

This starts a web interface at http://localhost:5173 where you can:

- **View Tools**: See all 23 registered tools with schemas
- **Test Tools**: Execute tools with custom parameters
- **Inspect Requests**: View raw MCP protocol requests and responses
- **Debug Schemas**: Validate parameter types and requirements
- **Test Resources**: Access MCP resources and view their data

**Why not inside the container?**
- The MCP Inspector starts its own web server on port 5173
- It needs to connect to the MCP endpoint at http://localhost/mcp/laravel
- Running from host avoids port conflicts and networking complexity

### Automated Testing

```bash
# Run full test suite
./vendor/bin/sail test

# Run with coverage report
./vendor/bin/sail artisan test --coverage

# Run specific test file
./vendor/bin/sail artisan test tests/Feature/Auth/AuthenticationTest.php

# Run tests in parallel
./vendor/bin/sail artisan test --parallel
```

### Manual Service Testing

```bash
./vendor/bin/sail artisan tinker

>>> $userService = app(\App\Services\User\UserService::class);
>>> $userService->getUserStats();

>>> $salesService = app(\App\Services\Analytics\SalesAnalyticsService::class);
>>> $salesService->getSalesStats();

>>> $rfmService = app(\App\Services\Analytics\RfmAnalysisService::class);
>>> $rfmService->getRfmAnalysis(10);
```

### Claude Desktop Integration Testing

1. Ensure Docker containers are running: `./vendor/bin/sail ps`
2. Restart Claude Desktop completely
3. Verify MCP connection indicator appears
4. Test with natural language queries:
   ```
   "Run a health check on the application"
   "What are the current sales statistics?"
   "Show me all MCP tools available"
   ```

---

## Deployment

### Production Checklist

#### Environment Configuration

```env
# Production settings
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_GENERATED_KEY_HERE

# Database (use production credentials)
DB_HOST=your-production-database-host
DB_DATABASE=your_production_database
DB_USERNAME=your_production_user
DB_PASSWORD=strong_production_password

# Use Redis for better performance
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Redis configuration
REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password
REDIS_PORT=6379

# Security
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict

# Performance
CACHE_PREFIX=laravel_mcp_
```

#### Optimization Commands

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader (composer)
composer install --optimize-autoloader --no-dev

# Clear development caches first
php artisan optimize:clear
```

#### Security Considerations

**Critical for Production:**

1. **Authentication & Authorization**
   - Implement API token authentication for HTTP MCP endpoints
   - Use Laravel Sanctum or Passport for token management
   - Add role-based access control (RBAC) to MCP tools
   - Example: Restrict financial tools to executives only

2. **Input Validation**
   - All inputs already validated via MCP schemas
   - Add additional business rule validation in services
   - Sanitize outputs to prevent data leakage

3. **Rate Limiting**
   - Implement rate limiting for HTTP-based MCP endpoints
   - Prevent abuse and DoS attacks
   - Use Laravel's built-in rate limiting features

4. **Database Security**
   - MCP tools have direct database access
   - Use read-only database users for query-only tools
   - Implement database-level permissions
   - Enable query logging and monitoring

5. **HTTPS/TLS**
   - Always use SSL/TLS in production
   - Configure reverse proxy (Nginx, Apache) with valid certificates
   - Force HTTPS redirects

6. **Audit Logging**
   - Log all MCP tool invocations with user context
   - Track data access patterns
   - Monitor for suspicious activity
   - Retain logs for compliance requirements

7. **Environment Security**
   - Never commit `.env` files to version control
   - Use environment variable managers (AWS Secrets Manager, Vault)
   - Rotate credentials regularly
   - Restrict file permissions on production servers

#### Process Management

For HTTP-based MCP deployments, use process managers like Supervisor:

```ini
[program:laravel-mcp]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/artisan mcp:start laravel
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/laravel-mcp.log
stopwaitsecs=3600
```

**Note**: Claude Desktop integration uses STDIO transport via Docker, not requiring additional process management beyond Docker itself.

#### Deployment Architecture

```
┌─────────────────────────────────────┐
│         Load Balancer (HAProxy)      │
│              HTTPS (443)             │
└──────────────┬──────────────────────┘
               │
      ┌────────┴─────────┐
      │                  │
┌─────▼─────┐      ┌────▼──────┐
│  App 1    │      │  App 2    │
│  Laravel  │      │  Laravel  │
│  MCP      │      │  MCP      │
└─────┬─────┘      └────┬──────┘
      │                  │
      └────────┬─────────┘
               │
    ┌──────────▼───────────┐
    │   MySQL (Primary)     │
    │   + Read Replicas     │
    └──────────┬───────────┘
               │
    ┌──────────▼───────────┐
    │   Redis Cluster       │
    │   (Cache + Sessions)  │
    └──────────────────────┘
```

---

## Documentation

This project includes comprehensive documentation for different audiences:

| Document | Audience | Purpose |
|----------|----------|---------|
| [README.md](README.md) | All | Overview, quick start, features, technical reference |
| [ARCHITECTURE.md](ARCHITECTURE.md) | Developers, Architects | Detailed system architecture, design patterns, best practices |
| [BUSINESS_CASE.md](BUSINESS_CASE.md) | Executives, Investors | ROI analysis, market opportunity, competitive landscape |
| [DEMO_SCENARIOS.md](DEMO_SCENARIOS.md) | Sales, Marketing | Demo scripts for various audiences (4-5 min each) |
| [DEMO_PROMPTS.md](DEMO_PROMPTS.md) | Sales, Demo Users | Ready-to-use prompts in French for feature demonstrations |
| [PITCH_GUIDE.md](PITCH_GUIDE.md) | Sales, Business Dev | Elevator pitches, objection handling, closing techniques |
| [DOCUMENTATION_INDEX.md](DOCUMENTATION_INDEX.md) | All | Navigation guide to all documentation |

**Quick Links:**
- **Want to understand the architecture?** → [ARCHITECTURE.md](ARCHITECTURE.md)
- **Need business justification?** → [BUSINESS_CASE.md](BUSINESS_CASE.md)
- **Preparing a demo?** → [DEMO_SCENARIOS.md](DEMO_SCENARIOS.md)
- **Looking for demo prompts?** → [DEMO_PROMPTS.md](DEMO_PROMPTS.md)

---

## Troubleshooting

### Common Issues

#### Docker containers won't start

```bash
# Stop all containers
./vendor/bin/sail down

# Remove all containers and volumes
docker system prune -f

# Rebuild from scratch
./vendor/bin/sail build --no-cache
./vendor/bin/sail up -d
```

#### MCP tools not appearing in Claude Desktop

1. **Verify containers are running**:
   ```bash
   ./vendor/bin/sail ps
   ```

2. **Check container name in config**:
   ```bash
   docker ps
   # Copy the exact container name (e.g., laravel-mcp-server-laravel.test-1)
   # Update Claude Desktop config JSON with this exact name
   ```

3. **Restart Claude Desktop completely**:
   - Quit Claude Desktop (not just close window)
   - Wait 10 seconds
   - Reopen Claude Desktop

4. **Test endpoint manually**:
   ```bash
   # From host machine
   curl http://localhost/mcp/laravel
   ```

#### Permission errors

```bash
# Fix storage and cache permissions
./vendor/bin/sail artisan storage:link
sudo chown -R $USER:$USER storage bootstrap/cache

# Inside container
./vendor/bin/sail shell
chmod -R 775 storage bootstrap/cache
```

#### Database connection issues

```bash
# Check MySQL service status
./vendor/bin/sail ps

# View MySQL logs
./vendor/bin/sail logs mysql

# Test connection manually
./vendor/bin/sail mysql -u sail -p
# Password: password

# Verify database exists
./vendor/bin/sail mysql -e "SHOW DATABASES;"
```

#### MCP Inspector connection issues

```bash
# Ensure app is running
./vendor/bin/sail ps

# Test MCP endpoint
curl http://localhost/mcp/laravel

# Run Inspector from host (NOT from container)
npx @modelcontextprotocol/inspector http://localhost/mcp/laravel

# If port 5173 is in use, the Inspector will fail
# Stop Vite dev server or use a different port
```

#### Clear all caches

```bash
# Clear Laravel caches
./vendor/bin/sail artisan optimize:clear

# Clear Docker build cache
docker builder prune

# Clear composer cache
./vendor/bin/sail composer clear-cache
```

### Getting Help

- **MCP Protocol**: [modelcontextprotocol.io](https://modelcontextprotocol.io)
- **Laravel MCP Package**: [github.com/laravel/mcp](https://github.com/laravel/mcp)
- **Laravel Documentation**: [laravel.com/docs](https://laravel.com/docs)
- **Docker Logs**: `./vendor/bin/sail logs -f`
- **Laravel Logs**: `./vendor/bin/sail artisan pail` (real-time)

---

## Contributing

Contributions are welcome! Please follow these guidelines:

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/amazing-feature`)
3. **Commit** changes (`git commit -m 'Add amazing feature'`)
4. **Test** thoroughly (`./vendor/bin/sail test`)
5. **Format** code (`./vendor/bin/sail pint`)
6. **Push** to branch (`git push origin feature/amazing-feature`)
7. **Open** a Pull Request

**Code Standards:**
- Follow PSR-12 coding standard (enforced by Laravel Pint)
- Write tests for new features
- Update documentation for user-facing changes
- Maintain clean architecture principles (see [ARCHITECTURE.md](ARCHITECTURE.md))

---

## License

This project is licensed under the **MIT License**. See [LICENSE](https://opensource.org/licenses/MIT) for details.

---

## Resources

- **Laravel MCP Package**: [github.com/laravel/mcp](https://github.com/laravel/mcp)
- **MCP Specification**: [modelcontextprotocol.io](https://modelcontextprotocol.io)
- **MCP Clients**: [modelcontextprotocol.io/clients](https://modelcontextprotocol.io/clients)
- **Laravel Documentation**: [laravel.com/docs](https://laravel.com/docs)
- **Laravel Sail**: [laravel.com/docs/sail](https://laravel.com/docs/sail)
- **Claude Desktop**: [claude.ai/download](https://claude.ai/download)

---

<div>

**Built with Laravel 12 • PHP 8.4 • MySQL 8.0 • MCP Protocol**

*Transforming business intelligence through conversational AI*

</div>
