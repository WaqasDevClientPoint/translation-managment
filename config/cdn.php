<?php

return [
    'enabled' => env('CDN_ENABLED', false),
    
    'disk' => env('CDN_DISK', 's3'),
    
    'url' => env('CDN_URL'),
    
    'cache_duration' => env('CDN_CACHE_DURATION', 3600),
    
    'paths' => [
        'translations' => 'translations',
        'exports' => 'exports',
    ],
]; 