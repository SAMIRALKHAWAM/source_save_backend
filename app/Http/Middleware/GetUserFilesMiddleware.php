<?php

namespace App\Http\Middleware;

use App\Http\Requests\Files\GetUserFilesRequest;
use App\Models\GroupUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class GetUserFilesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestFile = \app(GetUserFilesRequest::class);
        $group_id = $request->input('groupId');
        $user_id = $request->input('userId');
        $group_user = GroupUser::where(
            [
                'group_id' => $group_id,
                'user_id' => $user_id,
            ]
        )->first();
        if (!$group_user) {
            throw new AccessDeniedHttpException('Access Denied : Dont Have Permission');
        }
        $request->attributes->set('group_user',$group_user);
        $request->attributes->set('group_id',$group_user);
        $request->attributes->set('user_id',$group_user);
        return $next($request);
    }
}
