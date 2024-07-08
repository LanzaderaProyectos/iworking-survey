<?php

namespace MattDaneshvar\Survey\Helpers;

use MattDaneshvar\Survey\Library\Constants;

class Helpers
{
    /**
     * Returns an array of Survey status
     *
     * @return array
     */
    public static function buildSurveyStatusArray(): array
    {
        return [
            Constants::SURVEY_STATUS_DRAFT => 'Borrador',
            Constants::SURVEY_STATUS_PROCESS => 'En proceso',
            Constants::SURVEY_STATUS_COMPLETED => 'Terminada',
            Constants::SURVEY_STATUS_SEND_ERROR => 'Error',
        ];
    }

    /**
     * Returns an array of Entry status
     *
     * @return array
     */
    public static function buildEntryStatusArray(): array
    {
        return [
            Constants::ENTRY_STATUS_PENDING => 'Pendiente',
            Constants::ENTRY_STATUS_COMPLETED => 'Completada',
            Constants::ENTRY_STATUS_ERROR => 'Error',
        ];
    }
}
