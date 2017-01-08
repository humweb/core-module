<?php

namespace Humweb\Core\Http\Controllers;

use Humweb\Breadcrumbs\Breadcrumbs;
use Humweb\Settings\Facades\Settings;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $layout = 'layouts.default';
    protected $breadcrumbs;
    protected $currentUser;
    protected $settings;


    /**
     * Controller constructor.
     */
    public function __construct()
    {
        // Setup site settings
        $this->settings = Settings::getSection('site');
        $this->viewShare('siteSettings', $this->settings);

        // Set language
        app()->setLocale(array_get($this->settings, 'site.lang', 'en'));

        // Setup User
//        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();

//            return $next($request);
//        });

        $this->viewShare('currentUser', $this->currentUser);

        // Root Breadcrumb
        $this->crumb('Home', '/');
    }


    /**
     * Show the user profile.
     */
    public function setContent($view, $data = [])
    {
        if ($this->breadcrumbs) {
            $this->viewShare('breadcrumbs', $this->breadcrumbs);
        }

        if ( ! is_null($this->layout)) {
            if ($data instanceof Arrayable) {
                $data = $data->toArray();
            }

            return $this->layout->nest('child', $view, is_array($data) ? $data : []);
        }

        return view($view, $data);
    }


    /**
     * Set the layout used by the controller.
     *
     * @param $name
     */
    protected function setLayout($name)
    {
        $this->layout = is_string($name) ? view($name) : $name;
    }


    /**
     * Setup the layout used by the controller.
     */
    protected function setupLayout()
    {
        if ( ! is_null($this->layout) && ! is_object($this->layout)) {
            $this->layout = view($this->layout);
        }
    }


    /**
     * Setup the layout used by the controller.
     */
    protected function viewShare($key, $data)
    {
        view()->share($key, $data);
    }


    public function setTitle($title)
    {
        $this->viewShare('title', $title);
    }


    /**
     * Add Breadcrumb.
     *
     * @param      $label
     * @param null $link
     *
     * @return $this
     */
    protected function crumb($label, $link = null)
    {
        if ( ! $this->breadcrumbs) {
            $this->breadcrumbs = new Breadcrumbs();
        }

        $this->breadcrumbs->add($label, $link);

        return $this;
    }


    public function callAction($method, $parameters)
    {
        $this->setupLayout();

        $response = call_user_func_array(array($this, $method), $parameters);

        if (is_null($response) && ! is_null($this->layout)) {
            $response = $this->layout;
        }

        return $response;
    }
}
