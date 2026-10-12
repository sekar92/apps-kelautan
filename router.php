<?php
/**
 * Router script for PHP Built-in Server
 * Usage: php -S localhost:8000 router.php
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static files directly if they exist
$filePath = __DIR__ . $uri;
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false; // let the built-in server serve the static file
}

// Pass all other dynamic requests to Front Controller
require_once __DIR__ . '/index.php';
