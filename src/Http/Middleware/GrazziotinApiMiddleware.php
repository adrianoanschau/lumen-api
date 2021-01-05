<?php

namespace Grazziotin\GrazziotinApi\Http\Middleware;

use Closure;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class GrazziotinApiMiddleware
{
    public function handle($request, Closure $next)
    {
        $json_format = 'application/vnd.api+json';
        if ($request->header('Accept') != $json_format) {
            throw new NotAcceptableHttpException();
        }
        if ($request->method() !== 'GET' && $request->header('Content-Type') != $json_format) {
            throw new NotAcceptableHttpException();
        }

        return $next($request);
    }
}
