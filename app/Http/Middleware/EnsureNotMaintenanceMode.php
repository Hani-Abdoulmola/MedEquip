<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * When maintenance mode is on, only admins can access the application.
 * Non-admins are redirected to login with a message.
 */
class EnsureNotMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Setting::getBoolean('maintenance_mode', false)) {
            return $next($request);
        }

        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->hasRole('Admin')) {
            return $next($request);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('message', 'المنصة حاليا في وضع الصيانة، ستعود للعمل قريبا');
    }
}
