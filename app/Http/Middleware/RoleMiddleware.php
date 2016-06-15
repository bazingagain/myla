<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    /**
     * 中间件还可以接收额外的自定义参数:role
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->getUser() == "xiao") {
            return redirect('home');
        }
//        return $next($request);

    }
}
