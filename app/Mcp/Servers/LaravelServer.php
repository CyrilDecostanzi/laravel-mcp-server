<?php

namespace App\Mcp\Servers;

use App\Mcp\Resources\AppSettingsResource;
use App\Mcp\Resources\LaravelInfoResource;
use App\Mcp\Tools\ApplyDiscountTool;
use App\Mcp\Tools\CreateOrderTool;
use App\Mcp\Tools\CreateProductTool;
use App\Mcp\Tools\CreateUserTool;
use App\Mcp\Tools\GetCustomerInsightsTool;
use App\Mcp\Tools\GetDatabaseInfoTool;
use App\Mcp\Tools\GetInventoryAlertsTool;
use App\Mcp\Tools\GetInvoiceDetailsTool;
use App\Mcp\Tools\GetProductInventoryTool;
use App\Mcp\Tools\GetProductRecommendationsTool;
use App\Mcp\Tools\GetRevenueByPeriodTool;
use App\Mcp\Tools\GetRfmAnalysisTool;
use App\Mcp\Tools\GetSalesForecastTool;
use App\Mcp\Tools\GetSalesStatsTool;
use App\Mcp\Tools\GetSystemInfoTool;
use App\Mcp\Tools\GetTopProductsTool;
use App\Mcp\Tools\GetTrendingProductsTool;
use App\Mcp\Tools\GetUserStatsTool;
use App\Mcp\Tools\HealthCheckTool;
use App\Mcp\Tools\SearchOrdersTool;
use App\Mcp\Tools\SearchUsersTool;
use App\Mcp\Tools\UpdateOrderStatusTool;
use App\Mcp\Tools\UpdateProductStockTool;
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
    protected string $version = '3.0.0';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = <<<'MARKDOWN'
        This is an advanced Laravel E-commerce MCP server providing comprehensive business intelligence, predictive analytics, and data management capabilities.

        **📊 Advanced Analytics Tools:**
        - get_rfm_analysis: Perform RFM (Recency, Frequency, Monetary) customer segmentation with actionable insights
        - get_sales_forecast: Forecast future sales with trend analysis and confidence metrics
        - get_customer_insights: Get customer analytics, segments, and lifetime value

        **🛍️ Product Intelligence Tools:**
        - get_product_recommendations: Personalized recommendations (for_customer, cross_sell, upsell)
        - get_trending_products: Get trending products based on recent sales performance
        - get_top_products: Get best-selling products by quantity or revenue

        **📈 Sales Analytics Tools:**
        - get_sales_stats: Get comprehensive sales dashboard metrics and trends
        - get_revenue_by_period: Get revenue breakdown by daily/weekly/monthly periods

        **✏️ Order Management (READ & WRITE):**
        - search_orders: Search orders with flexible filtering (status, date, amount, customer)
        - create_order: Create new orders with stock validation and auto-calculation
        - update_order_status: Update order status (pending, processing, completed, cancelled, failed)
        - get_invoice_details: Get detailed invoice information with payment tracking

        **📦 Inventory Management (READ & WRITE):**
        - get_inventory_alerts: Get alerts for low stock, out of stock, and overdue invoices
        - get_product_inventory: Search and filter products with inventory details
        - update_product_stock: Update stock levels (set, add, subtract operations)
        - create_product: Create new products with auto-generated SKU
        - apply_discount: Apply percentage discounts to products

        **👥 User Management:**
        - get_user_stats: Get user statistics and distribution
        - search_users: Search for users by name or email
        - create_user: Create a new user account with validation

        **🔧 System Tools:**
        - get_system_info: Get comprehensive application and system information
        - get_database_info: Get detailed database statistics and table information
        - health_check: Get application health check information

        **Available Resources:**
        - config://app/settings: Application configuration settings
        - system://laravel/info: Laravel version and environment information

        **Key Features:**
        - Predictive analytics with sales forecasting
        - Customer segmentation using RFM analysis
        - Intelligent product recommendations (collaborative filtering)
        - Full CRUD operations on orders, products, and inventory
        - Real-time inventory management with stock validation
        - Comprehensive business insights and trends
    MARKDOWN;

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        // Advanced Analytics
        GetRfmAnalysisTool::class,
        GetSalesForecastTool::class,

        // Product Intelligence
        GetProductRecommendationsTool::class,
        GetTrendingProductsTool::class,
        GetTopProductsTool::class,

        // Sales Analytics
        GetSalesStatsTool::class,
        GetRevenueByPeriodTool::class,
        GetCustomerInsightsTool::class,

        // Order Management (Read & Write)
        SearchOrdersTool::class,
        CreateOrderTool::class,
        UpdateOrderStatusTool::class,
        GetInvoiceDetailsTool::class,

        // Inventory Management (Read & Write)
        GetInventoryAlertsTool::class,
        GetProductInventoryTool::class,
        UpdateProductStockTool::class,
        CreateProductTool::class,
        ApplyDiscountTool::class,

        // User Management
        GetUserStatsTool::class,
        SearchUsersTool::class,
        CreateUserTool::class,

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
