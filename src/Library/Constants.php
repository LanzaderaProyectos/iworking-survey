<?php

namespace MattDaneshvar\Survey\Library;

class Constants
{
    // ORDER STATUS
    const SURVEY_STATUS_DRAFT                           = 0; // Borrador de encuesta
    const SURVEY_STATUS_PROCESS                         = 1; // Encuesta en proceso
    const SURVEY_STATUS_COMPLETED                       = 2; // Encuesta completada
    const SURVEY_STATUS_SEND_ERROR                      = 99; // Encuesta error
    const SURVEY_STATUS_CLOSED                          = 98; // Cerrar encuesta


    const ENTRY_STATUS_PENDING                          = 1; // Entrada pendiente
    const ENTRY_STATUS_COMPLETED                        = 2; // Entrada completada
    const ENTRY_STATUS_ERROR                            = 99; // Entrada error



}
