<?php

namespace App\Http\Middleware;

use Closure;
use Session;
class IsLogin
{
    public function handle($request, Closure $next)
    {
      if(!$request->session()->has('account')){
        return redirect(route('admin_login')); 
      }
      return $next($request);
    }
}
