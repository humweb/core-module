<?php

namespace Humweb\Core\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AllowOnly
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param array                    $permissions
     * @param null                     $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions = [], $guard = null)
    {
        $action = $request->route()->getAction();
        $isUser = Auth::guard($guard)->user();
        $url = array_get($action, 'redirect_url', !$isUser ? '/login' : '/');

        if (!$isUser || !$isUser->getPermission()->hasPermission($permissions)) {
            $message = array_get($action, 'message', 'Not enough access..');

            if ($request->ajax()) {
                return response($message, 401);
            } else {
                return redirect($url)->with('warning', $message);
            }
        }

        return $next($request);
    }
}
