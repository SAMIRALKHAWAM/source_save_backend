<?php

namespace App\Http\Middleware;

use App\Http\Requests\User\Auth\ResetPasswordRequest;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestFile = ResetPasswordRequest::class;
        $email = $request->input('email');
        $code = $request->input('code');

        $user = User::where('email', $email)->first();
        if (Hash::check(!$code, $user->Code?->code) || !\now()->between($user->Code?->from_date_time, $user->Code?->to_date_time)) {
            throw ValidationException::withMessages(['Code Wrong Or Expired']);
        }
        $request->attributes->set('user',$user);
        return $next($request);
    }
}
