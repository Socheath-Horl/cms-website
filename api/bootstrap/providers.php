<?php

use App\Providers\AppServiceProvider;
use Dedoc\Scramble\ScrambleServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    Dedoc\Scramble\ScrambleServiceProvider::class,
    Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
];
