<?php

use App\Providers\AppServiceProvider;
use Dedoc\Scramble\ScrambleServiceProvider;

return [
    AppServiceProvider::class,
    ScrambleServiceProvider::class,
    Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
];
