<?php

return [
    'url' => env('SEO_URL', 'https://piramida.edu.al'),
    'indexable' => env('SEO_INDEXABLE', env('APP_ENV') === 'production'),
];
