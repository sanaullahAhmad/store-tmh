<?php

namespace App\Http\Controllers;

use App\Menu;
use Illuminate\Http\Request;

use App\Http\Requests;

class MenuController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return Menu::all()->toJson();
    }
}
