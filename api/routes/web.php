<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs/scalar', function () {
    $specUrl = url('/docs/api.json');

    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>CMS API Reference</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        body { margin: 0; }
    </style>
</head>
<body>
    <div id="api-reference" data-url="{$specUrl}"></div>
    <script src="https://cdn.jsdelivr.net/npm/@scalar/api-reference/dist/browser/standalone.js"></script>
</body>
</html>
HTML;
});
