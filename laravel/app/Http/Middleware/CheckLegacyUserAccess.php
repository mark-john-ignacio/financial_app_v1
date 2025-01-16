<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckLegacyUserAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Ensure the session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['timestamp'])) {
            $autologout = 3600; // 60 minutes of inactivity
            $lastactive = $_SESSION['timestamp'];
            if (time() - $lastactive > $autologout) {
                // Clear the session data
                $_SESSION = array();
                setcookie(session_name(), '', time() - 3600);
                session_destroy();

                return redirect('https://' . $request->getHttpHost() . '/denied.php');
            } else {
                // Reset the timestamp
                $_SESSION['timestamp'] = time();

                // Check user access level for the page
                $employeeid = $_SESSION['employeeid'];
                $pageid = $request->route()->getName();

                // dd($pageid);
                $hasAccess = DB::table('users_access')
                    ->where('userid', $employeeid)
                    ->where('pageid', $pageid)
                    ->exists();

                if (!$hasAccess) {
                    return redirect('https://' . $request->getHttpHost() . '/include/deny.php');
                }
            }
        } else {
            return redirect('https://' . $request->getHttpHost() . '/denied.php');
        }
        return $next($request);
    }
}