<?php
/**
 * Dev server router - mimics .htaccess rewrites
 * Usage: php -S localhost:8888 router.php
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static files directly
if ($uri !== '/' && file_exists(__DIR__ . $uri) && !is_dir(__DIR__ . $uri)) {
    return false;
}

// Route rewrites (mirrors .htaccess)
$routes = [
    '#^/cam-image/(\d+)\.jpg$#' => '/cam-image.php?id=$1',
    '#^/cam/(\d+)$#'            => '/cam.php?id=$1',
    '#^/country/([^/]+)$#'      => '/search.php?country=$1',
    '#^/city/([^/]+)$#'         => '/search.php?city=$1',
    '#^/place/([^/]+)$#'        => '/search.php?tag=$1',
    '#^/manufacturer/([^/]+)$#' => '/search.php?manufacturer=$1',
    '#^/search$#'               => '/search.php',
    '#^/map$#'                  => '/map.php',
    '#^/about$#'                => '/about.php',
    '#^/contact$#'              => '/contact.php',
    '#^/privacy$#'              => '/privacy.php',
    '#^/sitemap\.xml$#'         => '/sitemap-generator.php',
    '#^/view/(\d+)$#'           => '/cam.php?id=$1',
];

foreach ($routes as $pattern => $target) {
    if (preg_match($pattern, $uri, $matches)) {
        $file = preg_replace($pattern, $target, $uri);
        // Parse target to separate file and query string
        $parts = explode('?', $file, 2);
        $scriptFile = $parts[0];
        if (isset($parts[1])) {
            parse_str($parts[1], $params);
            $_GET = array_merge($_GET, $params);
            $_SERVER['QUERY_STRING'] = http_build_query($_GET);
        }
        $_SERVER['SCRIPT_NAME'] = $scriptFile;
        $_SERVER['SCRIPT_FILENAME'] = __DIR__ . $scriptFile;
        require __DIR__ . $scriptFile;
        return true;
    }
}

// Default: serve index.php for root
if ($uri === '/') {
    require __DIR__ . '/index.php';
    return true;
}

// 404
http_response_code(404);
echo '<h1>404 Not Found</h1>';
return true;
