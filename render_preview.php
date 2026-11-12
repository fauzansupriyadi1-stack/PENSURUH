<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$html = view('welcome')->render();
$html = str_replace('http://localhost/', './', $html);
$html = str_replace('/images/sources/', './images/sources/', $html);
file_put_contents(__DIR__ . '/public/index.html', $html);
echo "RENDER_SUCCESS\n";

