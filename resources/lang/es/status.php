<?php

use MattDaneshvar\Survey\Library\Constants;

return [
    'survey' => [
        Constants::SURVEY_STATUS_DRAFT => 'Borrador',
        Constants::SURVEY_STATUS_PROCESS => 'En proceso',
        Constants::SURVEY_STATUS_COMPLETED => 'Terminada',
        Constants::SURVEY_STATUS_SEND_ERROR => 'Error',
    ],
    'entry' => [
        Constants::ENTRY_STATUS_PENDING => 'Pendiente',
        Constants::ENTRY_STATUS_COMPLETED => 'Completada',
        Constants::ENTRY_STATUS_ERROR => 'Error',
    ]
];
