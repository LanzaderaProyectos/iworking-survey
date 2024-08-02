<?php

namespace MattDaneshvar\Survey\Library;

class Constants
{
    // ORDER STATUS
    const SURVEY_STATUS_DRAFT                           = 0; // Borrador de Formulario
    const SURVEY_STATUS_PROCESS                         = 1; // Formulario en proceso
    const SURVEY_STATUS_COORD_APPROVE                   = 2; // Coordinador aprobado 
    const SURVEY_STATUS_MODIFY                          = 3; // Coordinador aprobado 
    const SURVEY_STATUS_LIDER_APPROVE                   = 4;
    const SURVEY_STATUS_COMPLETED                       = 5; // Formulario completado
    const SURVEY_STATUS_APPROVED                        = 5; // Formulario aprobado
    const SURVEY_STATUS_REJECTED                        = 6; // Formulario rechazado
    const SURVEY_STATUS_MODIFIED                          = 7; // Formulario enviado
    const SURVEY_STATUS_SEND_ERROR                      = 99; // Formulario error
    const SURVEY_STATUS_CLOSED                          = 98; // Cerrar Formulario


    const ENTRY_STATUS_PENDING                          = 1; // Entrada pendiente
    const ENTRY_STATUS_COMPLETED                        = 2; // Entrada completada
    const ENTRY_STATUS_ERROR                            = 99; // Entrada error
    



}
