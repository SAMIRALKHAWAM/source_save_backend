<?php

namespace App\Http\Middleware;

use App\Http\Requests\User\Auth\UserLoginRequest;
use App\Models\Admin;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class LoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestFile = \app(UserLoginRequest::class);
        $email = $request->input('email');
        $password = $request->input('password');
        $actor = User::where('email', $email)->first();
        $role = 'user';
        if (!$actor) {
            $actor = Admin::where('email', $email)->first();
            $role = 'admin';
        }
        if (!Hash::check($password, $actor->password)) {
            throw ValidationException::withMessages(['email or password not match']);
        }
        $request->attributes->set('actor', $actor);
        $request->attributes->set('role', $role);
        return $next($request);
    }
}
