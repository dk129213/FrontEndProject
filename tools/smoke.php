<?php

// Renders every exported page through the Laravel kernel and reports failures.
// Usage: php tools/smoke.php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->instance('request', Illuminate\Http\Request::create('/'));
$kernel->bootstrap();

$failed = 0;
$paths = config('export.paths');

foreach ($paths as $path) {
    $response = $kernel->handle(Illuminate\Http\Request::create('/'.ltrim($path, '/')));
    if ($response->getStatusCode() !== 200) {
        $failed++;
        $message = $response->exception?->getMessage() ?? '';
        echo "FAIL {$response->getStatusCode()} /{$path}: {$message}\n";
    }
}

echo $failed ? "{$failed} failed\n" : 'all '.count($paths)." pages OK\n";
