<?php

namespace App\Mcp\Tools;

use App\Services\Analytics\ProductRecommendationService;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetProductRecommendationsTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get intelligent product recommendations. Supports multiple recommendation types: for_customer (personalized based on purchase history), cross_sell (frequently bought together), and upsell (higher-priced alternatives).
    MARKDOWN;

    public function __construct(
        private readonly ProductRecommendationService $recommendationService
    ) {}

    /**
     * Handle the tool request.
     *
     * @throws \JsonException
     */
    public function handle(Request $request): Response
    {
        $type = $request->get('type', 'for_customer');
        $customerId = $request->get('customer_id');
        $productId = $request->get('product_id');
        $limit = $request->get('limit', 5);

        $result = match ($type) {
            'for_customer' => $this->recommendationService->getRecommendationsForCustomer($customerId, $limit),
            'cross_sell' => $this->recommendationService->getCrossSellProducts($productId, $limit),
            'upsell' => $this->recommendationService->getUpsellProducts($productId, $limit),
            default => ['error' => 'Invalid recommendation type'],
        };

        return Response::text(json_encode($result, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => $schema->string()
                ->description('Recommendation type')
                ->enum(['for_customer', 'cross_sell', 'upsell'])
                ->default('for_customer'),
            'customer_id' => $schema->integer()
                ->description('Customer ID (required for for_customer type)'),
            'product_id' => $schema->integer()
                ->description('Product ID (required for cross_sell and upsell types)'),
            'limit' => $schema->integer()
                ->description('Maximum number of recommendations')
                ->default(5),
        ];
    }
}
