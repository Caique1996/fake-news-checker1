<?php

namespace App\Http\Middleware;

use App\Enums\ApiStatusEnum;
use App\Exceptions\UserInfoException;
use App\Models\Api;
use App\Models\BlockedIp;
use Backpack\CRUD\app\Exceptions\AccessDeniedException;
use Closure;
use Doctrine\DBAL\Exception;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CheckBlockedIp
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

        $row = BlockedIp::isBlocked();
        if ($row) {
            $message = "The IP :ip has been blocked until :expires_at.";
            throw  new AccessDeniedException(__($message, ['ip' => $row->ip_address, 'expires_at' => formatUserDate($row->expires_at)]));
        }
        return $next($request);
    }
}
