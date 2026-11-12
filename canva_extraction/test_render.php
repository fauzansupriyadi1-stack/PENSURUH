<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rendered = view('welcome')->render();
file_put_contents(__DIR__ . '/public/rendered_preview.html', $rendered);
echo "Wrote " . strlen($rendered) . " bytes to public/rendered_preview.html\n";
