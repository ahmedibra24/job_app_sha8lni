<?php

use Illuminate\Console\Application as Artisan;
use Illuminate\Foundation\Providers\ArtisanServiceProvider;
use Illuminate\Foundation\Providers\ConsoleSupportServiceProvider;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

// Ensure we are using the Console Kernel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "Bootstrapping...\n";
$kernel->bootstrap();

echo "Checking Providers...\n";
echo "ArtisanServiceProvider loaded: " . ($app->getProvider(ArtisanServiceProvider::class) ? 'Yes' : 'No') . "\n";
echo "ConsoleSupportServiceProvider loaded: " . ($app->getProvider(ConsoleSupportServiceProvider::class) ? 'Yes' : 'No') . "\n";

echo "Checking Bootstrappers...\n";
$reflection = new ReflectionClass(Artisan::class);
$property = $reflection->getProperty('bootstrappers');
$property->setAccessible(true);
$bootstrappers = $property->getValue();
echo "Bootstrappers count: " . count($bootstrappers) . "\n";

echo "Creating Artisan instance...\n";
$artisan = new Artisan($app, $app['events'], 'test');
$commands = $artisan->all();
echo "Artisan commands count: " . count($commands) . "\n";

// Filter standard commands
$standard = ['serve', 'package:discover', 'optimize'];
foreach ($standard as $cmd) {
    echo "Command '$cmd' exists: " . ($artisan->has($cmd) ? 'Yes' : 'No') . "\n";
}
