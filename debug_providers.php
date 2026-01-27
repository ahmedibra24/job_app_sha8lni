<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

$config = $app->make('config');
echo "Providers count: " . count($config->get('app.providers') ?? []) . "\n";
print_r($config->get('app.providers'));
