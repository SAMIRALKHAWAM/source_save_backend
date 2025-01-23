<?php

namespace App\Http\Middleware;

use App\Http\Requests\File\GetFileEditByUserController;
use App\Models\GroupUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class GetUserEditedFilesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $requestFile = \app(GetFileEditByUserController::class);
        $group_id = $request->input('groupId');
        $user_id = $request->input('userId');
        $group_user = GroupUser::where([
            'group_id' => $group_id,
            'user_id' => $user_id,
        ])->first();
        if (!$group_user) {
            throw new AccessDeniedHttpException('Access Denied: Not In Group Or You Are Admin Of This Group');
        }
        $request->attributes->set('group_user',$group_user);
        return $next($request);
    }
}
