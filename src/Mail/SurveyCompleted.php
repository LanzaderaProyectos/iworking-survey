<?php

namespace MattDaneshvar\Survey\Mail;

use Illuminate\Mail\Mailable;
use MattDaneshvar\Survey\Models\Entry;
use MattDaneshvar\Survey\Models\Survey;
use Barryvdh\DomPDF\Facade as PDF;


class SurveyCompleted extends Mailable
{
    protected Entry $entry;
    /**
     * Create a new message instance.
     * @param Survey $order
     * @param $emailProvider
     */
    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        $data = [
            'answers' => $this->entry->answers,
            'lang'  => $this->entry->lang,
            'survey' => $this->entry->survey,
            'entry' => $this->entry
        ];
        $pdf = PDF::loadView('survey::exports.pdf-entry', $data);

        if ($this->entry->lang == 'en') {
            $subject = 'SURVEY ' . $this->entry->survey->getTranslation('name', 'en');
            $viewNotification = 'survey::emails.survey-completed-message-en';
        } else {
            $subject = 'ENCUESTA ' . $this->entry->survey->getTranslation('name', 'es');
            $viewNotification = 'survey::emails.survey-completed-message-es';
        }
        return $this->subject($subject)
            ->from(config('iworking-survey.mail-from.address'), config('iworking-survey.mail-from.address'))
            ->view($viewNotification)
            ->attachData($pdf->output(), $this->entry->survey->getTranslation('name', $this->entry->lang) . '.pdf');
    }
}
