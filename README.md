# Laravel E-commerce MCP Server

A **Laravel 12 E-commerce MCP (Model Context Protocol) server** providing comprehensive business intelligence, analytics, and data management tools for AI assistants like Claude.

## 🚀 Quick Start

```bash
# Start the application
./vendor/bin/sail up -d

# Seed e-commerce database (100 users, 200 products, 500 orders)
./vendor/bin/sail artisan migrate:fresh --seed

# Test MCP tools
./vendor/bin/sail artisan mcp:list
```

See [QUICK_START.md](QUICK_START.md) for detailed setup instructions.

## 📋 What's Included

### 15 MCP Tools

**User Management** (3 tools)
- **get_user_stats** - User statistics & distribution
- **search_users** - Search users by name/email
- **create_user** - Create new user accounts

**Sales Analytics** (4 tools)
- **get_sales_stats** - Comprehensive sales dashboard (revenue, orders, trends)
- **get_revenue_by_period** - Revenue breakdown (daily/weekly/monthly)
- **get_top_products** - Best-selling products by quantity or revenue
- **get_customer_insights** - Customer segments, LTV, and analytics

**Inventory & Alerts** (2 tools)
- **get_inventory_alerts** - Low stock, out of stock, overdue invoices
- **get_product_inventory** - Product search with inventory details

**Search & Details** (2 tools)
- **search_orders** - Flexible order filtering (status, date, amount, customer)
- **get_invoice_details** - Invoice information with payment tracking

**System** (3 tools)
- **get_system_info** - Laravel/PHP/system details
- **get_database_info** - Database tables & row counts
- **health_check** - Application health monitoring

### E-commerce Database Schema

- **Users** (101) - Customer accounts
- **Categories** (10) - Product classifications
- **Products** (200) - Inventory with pricing & stock
- **Orders** (500) - 6 months of order history
- **Order Items** (1472) - Line items with snapshots
- **Invoices** (207) - Billing documents
- **Payments** (101) - Payment transactions
- **Total Revenue**: €929,574.57

### 2 MCP Resources
- **config://app/settings** - Application configuration
- **system://laravel/info** - Laravel runtime information

## 🛠️ Tech Stack

- **Laravel**: 12.36.1
- **PHP**: 8.4.14
- **MySQL**: 8.0.32
- **Docker**: Laravel Sail
- **MCP**: php-mcp/laravel v0.3.2

## 📚 Documentation

- **[QUICK_START.md](QUICK_START.md)** - Get running in 5 minutes
- **[MCP_IMPLEMENTATION.md](MCP_IMPLEMENTATION.md)** - Complete implementation guide
- **[MCP_README.md](MCP_README.md)** - Full project overview
- **Documentation technique complète** disponible dans Obsidian

## 🔧 Usage with Claude Desktop

Add to your `claude_desktop_config.json`:

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

Restart Claude Desktop, then ask:
- "What are the current sales statistics?"
- "Show me top 10 products by revenue"
- "What inventory alerts do we have?"
- "Find orders from last month over €500"
- "Get customer insights and segments"
- "Show revenue trends for the last 6 months"

## 📦 Installation

```bash
# Clone repository
git clone <repo-url> laravel-mcp-server
cd laravel-mcp-server

# Install dependencies (if needed)
composer install

# Start Docker containers
./vendor/bin/sail up -d

# Run migrations and seed e-commerce data
./vendor/bin/sail artisan migrate:fresh --seed
```

## 🧪 Testing

```bash
# List all available MCP tools
./vendor/bin/sail artisan mcp:list

# Test sales statistics
# (Use via Claude Desktop for actual MCP testing)

# Run Laravel tests
./vendor/bin/sail test
```

## 📄 License

Open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

