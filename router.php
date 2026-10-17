<?php declare(strict_types=1);

// Dev router for PHP's built-in server:
//   php -S localhost:8000 -t www router.php
// Serves existing static files directly and routes everything else to the front controller.

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . '/www' . $path;

if ($path !== '/' && is_file($file)) {
	return false; // let the built-in server serve the static file
}

require __DIR__ . '/www/index.php';
