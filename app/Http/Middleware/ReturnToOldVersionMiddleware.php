<?php

namespace App\Http\Middleware;

use App\Http\Requests\OldFile\OldFileIdRequest;
use App\Models\OldFile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ReturnToOldVersionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestFile  = \app(OldFileIdRequest::class);
        $old_file_id = $request->input('old_file_id');
        $oldFile = OldFile::find($old_file_id);
        $file = $oldFile->File;
        if ($file->reserved_by != null) {
            throw new AccessDeniedHttpException('Access Denied : this File is UNAVAILABLE');
        }
        $request->attributes->set('oldFile',$oldFile);
        $request->attributes->set('file',$file);
        return $next($request);
    }
}
