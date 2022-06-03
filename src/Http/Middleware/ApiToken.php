<?php

namespace SMSkin\ServiceBus\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use SMSkin\ServiceBus\Exceptions\ApiTokenNotDefined;

class ApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return mixed
     * @throws ApiTokenNotDefined
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $token = config('smskin.service-bus.host.api_token');
        if (!$token) {
            throw new ApiTokenNotDefined();
        }

        if ($request->hasHeader('X-API-TOKEN') && $request->header('X-API-TOKEN') === $token) {
            return $next($request);
        }
        abort(403);
    }
}
