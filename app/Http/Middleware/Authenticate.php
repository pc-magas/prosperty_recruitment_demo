<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use \Illuminate\Auth\AuthenticationException;
use \Closure;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        try {
            $this->authenticate($request, $guards);
            return $next($request);  
        } catch(AuthenticationException $e){
            return new JsonResponse(['message'=>'Forbitten'],401);
        }catch(\Exception $e){
            throw $e;
        }

    }
}
