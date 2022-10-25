<?php

namespace MattDaneshvar\Survey\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use MattDaneshvar\Survey\Models\Entry;
use Illuminate\Contracts\Encryption\DecryptException;
use MattDaneshvar\Survey\Library\Constants;

class UserSurvey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $decrypted = Crypt::decryptString($request->route('user'));
        $decrypted = explode(';', $decrypted);
        // Pos [0] => email, Pos [1] => survey_id

        try {
            $entry = Entry::where('participant', $decrypted[0])
                ->where('survey_id', $decrypted[1])
                ->where('status', Constants::ENTRY_STATUS_PENDING)
                ->first();
            if ($entry) {
                return $next($request);
            } else {
                return redirect('/');
            }
        } catch (DecryptException $e) {
            Log::error($e);
            return redirect('/');
        }
    }
}
