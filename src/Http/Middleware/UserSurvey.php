<?php

namespace MattDaneshvar\Survey\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use MattDaneshvar\Survey\Models\Entry;
use MattDaneshvar\Survey\Library\Constants;
use Illuminate\Contracts\Encryption\DecryptException;

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
                ->whereHas('survey', function ($q) {
                    $q->whereDate('expiration', '>=', Carbon::now());
                })
                ->first();
            if ($entry) {
                return $next($request);
            } else {
                return redirect()->route('survey.not-available');
            }
        } catch (DecryptException $e) {
            Log::error($e);
            return redirect('/');
        }
    }
}
