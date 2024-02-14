<?php

namespace MattDaneshvar\Survey\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use MattDaneshvar\Survey\Models\Entry;
use MattDaneshvar\Survey\Models\Question;

class DecryptionService
{
    public function decryptUser(string $user): ?Entry
    {
        $decrypted  =  Crypt::decryptString($user);
        $decrypted  = explode(';', $decrypted);

        // Pos [0] => email, Pos [1] => survey_id
        return Entry::where('participant', $decrypted[0])
            ->where('survey_id', $decrypted[1])->first();
    }
}