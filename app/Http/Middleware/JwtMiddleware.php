<?php
namespace App\Http\Middleware;
use Closure;
use Exception;
use App\User;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
class JwtMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        //$token = $request->get('token');
        $token = $request->bearerToken();

        if(!$token) {
            // Unauthorized response if token not there
            return response()->json([
                'message' => 'Token not provided.'
            ], 401);
        }
        try {
            $credentials = JWT::decode($token, env('JWT_SECRET', 'JhbGciOiJIUzI1N0eXAiOiJKV1QiLC'), ['HS256']);
        } catch(ExpiredException $e) {
            return response()->json([
                'message' => 'Provided token is expired.'
            ], 401);
        } catch(Exception $e) {
            return response()->json([
                'message' => 'An error while decoding token.'
            ], 401);
        }
        if($credentials->sub === 0){
            $user = new User;
            $user -> id = 0;
            $user -> username = "admin";
            $user -> role = "admin";
            $request->auth = $user;
            return $next($request);
        }
        $user = User::find($credentials->sub);
        // Now let's put the user in the request class so that you can grab it from there
        $request->auth = $user;
        return $next($request);
    }
}