<?php

/*
 * Router for PHP's built-in web server ONLY (php -S).
 *
 *   php -S 127.0.0.1:8000 -t public public/router.php
 *
 * It serves real files under public/ (built JS/CSS, images, uploads) directly
 * and hands every other path to Symfony's front controller so both the JSON
 * API and client-side SPA routes resolve. Apache/nginx do NOT use this file —
 * they use public/index.php via public/.htaccess.
 */

$path = parse_url($_SERVER['REQUEST_URI'], \PHP_URL_PATH);

if ('/' !== $path && is_file(__DIR__.$path)) {
    return false; // let the built-in server serve the static asset as-is
}

require __DIR__.'/index.php';
