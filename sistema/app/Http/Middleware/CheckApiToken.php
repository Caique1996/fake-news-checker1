<?php

namespace App\Http\Middleware;

use App\Enums\ApiStatusEnum;
use App\Models\Api;
use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CheckApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if (!isFullEmpty(trim($request->header('api-token')))) {
            $apiToken = $request->header('api-token');
            $api = Api::where('token', $apiToken)->first();
            if (isset($api['id'])) {
                if ($api['status'] == ApiStatusEnum::Active->value) {
                    $allowedIps = $api->getAllowedIps();
                    $requestIp = getUserIp();
                    if (in_array($requestIp, $allowedIps)) {
                        return $next($request);
                    } else {
                        return defaultApiResponse(apiErrorMessage('This API has been disabled.'));
                    }
                } else {
                    return defaultApiResponse(apiErrorMessage('This API has been disabled.'));
                }
            }
        }
        return defaultApiResponse(apiErrorMessage('Invalid Token'));
    }
}
