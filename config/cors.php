<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    // 'paths' => ['api/*'],
    // 'allowed_origins' => ['*'],
    // 'supports_credentials' => true, // Allow credentials (e.g., cookies, tokens)
    // 'allowed_origins_patterns' => [], // Leave empty unless using dynamic subdomains
    // 'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization', 'Accept', 'Origin'],
    // 'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    // 'exposed_headers' => ['Authorization'], // Optional: expose custom headers
    // 'max_age' => 3600, // Cache preflight response for 1 hour

    'paths' => ['api/*'],
    // The React dev server (Vite) runs on 5173 by default — not 5500,
    // which is what this was previously pointed at (likely left over
    // from an earlier tool like Live Server). With only 5500 allowed,
    // every request from the actual React app gets silently blocked by
    // the browser's CORS check, which looks exactly like being stuck at
    // the login screen forever, since nothing past login can load
    // without a successful, readable API response.
    // Note: the React admin app no longer needs cross-origin access at
    // all — it's served from this same Laravel app now, at /admin, not
    // a separate app on a different port. These origins are left here
    // in case anything else (a future mobile app, or the old standalone
    // React dev setup if you still use it) needs cross-origin API
    // access; if nothing else does, this can be safely tightened.
    'allowed_origins' => ['http://localhost:5173', 'http://localhost:5500'],
    'supports_credentials' => true,
    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization', 'Accept', 'Origin'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    'exposed_headers' => ['Authorization'],
    'max_age' => 3600,



];
