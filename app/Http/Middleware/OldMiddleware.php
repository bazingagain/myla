<?php

namespace App\Http\Middleware;

use Closure;

class OldMiddleware
{
    /**
     * 处理请求过滤器
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->input('age') <= 200) {
            return redirect('home');
        }
        //请求向下传递
        return $next($request);
    }
}
