<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function index()
    {
        return response()->json([
            'site_name' => 'CMS Website',
            'version' => '1.0.0',
            'description' => 'Content Management System',
        ]);
    }
}
