<?php

namespace Humweb\Core\Http\Controllers;

class HomeController extends AdminController
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->setContent('home');
    }
}
