<?php

namespace App\Controllers;

use App\Helpers\Helper;

class IndexController
{
    // Index View
    public function index()
    {
        return Helper::view('frontend/index.php');
    }
}