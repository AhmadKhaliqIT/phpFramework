<?php

namespace App\Controllers;

use Core\Blade\Blade;
use Core\Blade\View;
use Core\Database\DB;
use Core\DataTables\DataTables;
use Core\FileSystem\FileSystem;
use Core\Http\Request;
use Core\Http\Response;
use Core\Auth\Auth;
use Exception;

class ExampleController extends Controller
{
    public function ExampleFunction()
    {
        return view('test');
    } 
}