<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // 企業側のルートの場合
            if ($request->is('company/*')) {
                return route('company.login.form');
            }
            
            // 求職者側のルートの場合  
            if ($request->is('job_seeker/*')) {
                return route('job_seeker.login.form');
            }
            
            // デフォルト（トップページなど）
            return route('job_seeker.login.form');
        }
    }
}