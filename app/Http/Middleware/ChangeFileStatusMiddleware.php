<?php

namespace App\Http\Middleware;

use App\Http\Requests\Files\ChangeFileStatusRequest;
use App\Models\File;
use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ChangeFileStatusMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestFile = ChangeFileStatusRequest::class;
        $user = \auth('user')->user();
        $fileId = $request->input('file_id');
        $file = File::find($fileId);
        $group_id = $file->GroupUser->group_id;
        $group_Admin = Group::GroupAdmin($group_id);
        if ($group_Admin->user_id != $user->id) {
            throw new AccessDeniedHttpException('Access Denied : Dont Have Permission');
        }
        $request->attributes->set('file',$file);
        $request->attributes->set('user',$user);
        return $next($request);
    }
}
