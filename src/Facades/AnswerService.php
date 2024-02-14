<?php

namespace MattDaneshvar\Survey\Facades;

use Illuminate\Support\Facades\Facade;

class AnswerService extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'answerService';
    }
}
