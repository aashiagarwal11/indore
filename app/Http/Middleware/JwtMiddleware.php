<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Support\Facades\Auth;



class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->role_id != 1) {
            $_error = '';
            try {
                if (!JWTAuth::parseToken()->authenticate()) {
                    throw new TokenInvalidException();
                }
            } catch (TokenInvalidException $e) {
                $_error = 'Token Is Invalid';
            } catch (TokenExpiredException $e) {
                $_error = 'Token Expired';
            } catch (\Exception $e) {
                // catch on "token not found"
                $routeName = $request->route()->getName();

                $_error = (!empty($routeName) && strpos($routeName, 'api.guest') === 0)
                    ? '' : 'Token Not Found';
            }

            if ($_error !== '') {
                return response()->json([
                    'message' => 'Unauthorized',
                ]);
                // return ApiResponse::unauthorized($_error);
            }
        }

        return $next($request);
    }
}
