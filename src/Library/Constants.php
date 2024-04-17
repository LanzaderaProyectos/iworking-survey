<?php

namespace MattDaneshvar\Survey\Library;

class Constants
{
    // ORDER STATUS
    const SURVEY_STATUS_DRAFT                           = 0; // Borrador de Formulario
    const SURVEY_STATUS_PROCESS                         = 1; // Formulario en proceso
    const SURVEY_STATUS_COMPLETED                       = 2; // Formulario completado
    const SURVEY_STATUS_SEND_ERROR                      = 99; // Formulario error
    const SURVEY_STATUS_CLOSED                          = 98; // Cerrar Formulario


    const ENTRY_STATUS_PENDING                          = 1; // Entrada pendiente
    const ENTRY_STATUS_COMPLETED                        = 2; // Entrada completada
    const ENTRY_STATUS_ERROR                            = 99; // Entrada error



}
