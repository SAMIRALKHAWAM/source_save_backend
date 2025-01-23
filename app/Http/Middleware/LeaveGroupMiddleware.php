<?php

namespace App\Http\Middleware;

use App\Http\Requests\Group\GroupIdRequest;
use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class LeaveGroupMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $customRequest = \app(GroupIdRequest::class);

        $user = \auth('user')->user();
        $groupId = $request->input('groupId');

        $group = Group::where('id', $groupId)
            ->whereHas('GroupUsers', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('is_admin', 0);
            })->first();

        if (!$group) {
            throw new AccessDeniedHttpException('Access Denied: Not In Group Or You Are Admin Of This Group');
        }

        $request->attributes->set('group', $group);
        return $next($request);
    }
}
