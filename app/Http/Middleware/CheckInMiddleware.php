<?php

namespace App\Http\Middleware;

use App\Http\Requests\Files\CheckInRequest;
use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CheckInMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestFile = CheckInRequest::class;
        $user = \auth('user')->user();
        $group_id = $request->input('group_id');
        $group_Admin = Group::GroupAdmin($group_id);
        if ($group_Admin->user_id != $user->id && !$user->hasPermissionTo('Edit_File')) {
            throw new AccessDeniedHttpException('Access Denied : Dont Have Permission');
        }
        $request->attributes->set('user',$user);
        return $next($request);
    }
}
