<?php

namespace hcolab\cms\middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;


class CMSAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $admin = session('admin');

        if(!$admin){
            return redirect()->route('login')->with('notification', 'Login is required');
        }

        if(Hash::check('admin', $admin->password) ){
            return redirect()->route('force-change-password');
        }
      
        request()->merge(['admin' => $admin]);
    
        return $next($request);
    }
}