<?php

namespace Humweb\Core\Http\Controllers;

use Humweb\Settings\Facades\Settings;
use Humweb\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

abstract class ApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ApiResponse;

    protected $currentUser;
    protected $settings;
    protected $layout = null;


    /**
     * Controller constructor.
     */
    public function __construct()
    {

        // Setup site settings
        $this->settings = Settings::getSection('site');

        // Set language
        app()->setLocale(array_get($this->settings, 'site.lang', 'en'));

        // Setup User
        //        $this->middleware(function ($request, $next) {
        $this->currentUser = Auth::user();

        //            return $next($request);
        //        });

    }
}
