<?php

use App\Mcp\Servers\LaravelServer;
use Laravel\Mcp\Facades\Mcp;

// Register a local MCP server (for Claude Desktop and other local clients)
Mcp::local('laravel', LaravelServer::class);

// Register a web MCP server (for HTTP-based clients)
Mcp::web('/mcp/laravel', LaravelServer::class);
