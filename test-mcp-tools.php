<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Laravel E-commerce MCP Server - Tools Verification\n";
echo str_repeat("=", 60) . "\n\n";

// Load the server configuration
$serverClass = \App\Mcp\Servers\LaravelServer::class;
$reflection = new ReflectionClass($serverClass);

// Get protected properties
$nameProperty = $reflection->getProperty('name');
$nameProperty->setAccessible(true);
$versionProperty = $reflection->getProperty('version');
$versionProperty->setAccessible(true);
$toolsProperty = $reflection->getProperty('tools');
$toolsProperty->setAccessible(true);

$instance = $reflection->newInstanceWithoutConstructor();

echo "📦 Server Name: " . $nameProperty->getValue($instance) . "\n";
echo "📌 Version: " . $versionProperty->getValue($instance) . "\n";
echo "\n";

$tools = $toolsProperty->getValue($instance);
echo "🛠️  Registered Tools (" . count($tools) . "):\n";
echo str_repeat("-", 60) . "\n";

foreach ($tools as $index => $toolClass) {
    $toolName = class_basename($toolClass);
    $toolReflection = new ReflectionClass($toolClass);
    
    // Get tool description if available
    $descProperty = $toolReflection->getProperty('description');
    $descProperty->setAccessible(true);
    $toolInstance = $toolReflection->newInstanceWithoutConstructor();
    $description = $descProperty->getValue($toolInstance);
    
    // Extract first line of description
    $firstLine = explode("\n", trim($description))[0];
    
    printf("%2d. %-35s\n", $index + 1, $toolName);
    echo "    " . $firstLine . "\n\n";
}

echo str_repeat("=", 60) . "\n";
echo "✅ All tools loaded successfully!\n";
