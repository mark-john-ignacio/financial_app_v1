<?php 


namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthenticateLegacyUser
{
    public function handle($request, Closure $next)
    {
        // Ensure the session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if the user is logged in to the legacy system
        if (isset($_SESSION['employeeid']))
        {
            // Check if the user is already authenticated in Laravel
            
            $currentLaravelUser = Auth::user();
            $legacyEmployeeId = $_SESSION['employeeid'];

            if($currentLaravelUser){
                // If the current Laravel user is different from the legacy user, log out the current Laravel user
                if($currentLaravelUser->Userid !== $legacyEmployeeId){
                    Auth::logout();
                }
            }


            // Authenticate the user in Laravel
            if (!Auth::check()) {
                $user = User::where('Userid', $_SESSION['employeeid'])->first();
                if ($user) {
                    Auth::login($user);
                    foreach ($_SESSION as $key => $value) {
                        session()->put($key, $value);
                    }
                }
            }
        } else {
            // If the user is not logged in to the legacy system, log out the current Laravel user
            Auth::logout();
            session()->flush();
        }

        return $next($request);
    }
}