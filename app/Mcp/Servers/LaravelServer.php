<?php

namespace App\Mcp\Servers;

use App\Mcp\Resources\AppSettingsResource;
use App\Mcp\Resources\LaravelInfoResource;
use App\Mcp\Tools\CreateUserTool;
use App\Mcp\Tools\GetCustomerInsightsTool;
use App\Mcp\Tools\GetDatabaseInfoTool;
use App\Mcp\Tools\GetInventoryAlertsTool;
use App\Mcp\Tools\GetInvoiceDetailsTool;
use App\Mcp\Tools\GetProductInventoryTool;
use App\Mcp\Tools\GetRevenueByPeriodTool;
use App\Mcp\Tools\GetSalesStatsTool;
use App\Mcp\Tools\GetSystemInfoTool;
use App\Mcp\Tools\GetTopProductsTool;
use App\Mcp\Tools\GetUserStatsTool;
use App\Mcp\Tools\HealthCheckTool;
use App\Mcp\Tools\SearchOrdersTool;
use App\Mcp\Tools\SearchUsersTool;
use Laravel\Mcp\Server;

class LaravelServer extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'Laravel E-commerce MCP Server';

    /**
     * The MCP server's version.
     */
    protected string $version = '2.0.0';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = <<<'MARKDOWN'
        This is a Laravel E-commerce MCP server providing comprehensive business intelligence and data management tools.

        **User Management Tools:**
        - get_user_stats: Get user statistics and distribution
        - search_users: Search for users by name or email
        - create_user: Create a new user account with validation

        **Sales Analytics Tools:**
        - get_sales_stats: Get comprehensive sales dashboard metrics and trends
        - get_revenue_by_period: Get revenue breakdown by daily/weekly/monthly periods
        - get_top_products: Get best-selling products by quantity or revenue
        - get_customer_insights: Get customer analytics, segments, and lifetime value

        **Inventory & Alerts Tools:**
        - get_inventory_alerts: Get alerts for low stock, out of stock, and overdue invoices
        - get_product_inventory: Search and filter products with inventory details

        **Search & Details Tools:**
        - search_orders: Search orders with flexible filtering (status, date, amount, customer)
        - get_invoice_details: Get detailed invoice information with payment tracking

        **System Tools:**
        - get_system_info: Get comprehensive application and system information
        - get_database_info: Get detailed database statistics and table information
        - health_check: Get application health check information

        **Available Resources:**
        - config://app/settings: Application configuration settings
        - system://laravel/info: Laravel version and environment information
    MARKDOWN;

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        // User Management
        GetUserStatsTool::class,
        SearchUsersTool::class,
        CreateUserTool::class,
        
        // Sales Analytics
        GetSalesStatsTool::class,
        GetRevenueByPeriodTool::class,
        GetTopProductsTool::class,
        GetCustomerInsightsTool::class,
        
        // Inventory & Alerts
        GetInventoryAlertsTool::class,
        GetProductInventoryTool::class,
        
        // Search & Details
        SearchOrdersTool::class,
        GetInvoiceDetailsTool::class,
        
        // System
        GetSystemInfoTool::class,
        GetDatabaseInfoTool::class,
        HealthCheckTool::class,
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Resource>>
     */
    protected array $resources = [
        AppSettingsResource::class,
        LaravelInfoResource::class,
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Prompt>>
     */
    protected array $prompts = [
        //
    ];
}
