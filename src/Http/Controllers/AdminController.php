<?php

namespace Humweb\Core\Http\Controllers;

use Humweb\Html\Facades\AdminMenu;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $layout = 'layouts.admin';
    protected $menu;


    /**
     * Controller constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->menu = AdminMenu::getFacadeRoot();

        $this->crumb('Admin', '/');
        $this->viewShare('admin_menu', $this->menu->render());
    }


    public function getIndex(Request $request)
    {
        return $this->setContent('admin.dashboard');
    }
}
