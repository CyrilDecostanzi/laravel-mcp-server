# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview




```bash
./vendor/bin/sail up -d

# Stop containers
./vendor/bin/sail down

```


```bash
# Run migrations
./vendor/bin/sail artisan migrate

./vendor/bin/sail artisan migrate:fresh --seed

./vendor/bin/sail mysql

```


```bash
# Run test suite
./vendor/bin/sail test

# Run specific test file
./vendor/bin/sail artisan test tests/Feature/Auth/AuthenticationTest.php
```




```php
    public function __construct(
        private readonly SalesAnalyticsService $salesAnalyticsService
    ) {}
```





## Creating New MCP Tools


```bash
```

```bash
```

```php
<?php

namespace App\Mcp\Tools;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

{

    public function __construct(
    ) {}

    public function handle(Request $request): Response
    {


    }

    public function schema(): array
    {
        return [
            ],
        ];
    }
}
```

4. **Register in `app/Mcp/Servers/LaravelServer.php`**:
```php
protected array $tools = [
    // ... existing tools
];
```

5. **Test with MCP Inspector**:
```bash
./vendor/bin/sail artisan mcp:inspector laravel
```

## Code Quality Standards
















