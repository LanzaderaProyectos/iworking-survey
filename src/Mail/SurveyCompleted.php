<?php

namespace MattDaneshvar\Survey\Mail;

use Illuminate\Mail\Mailable;
use MattDaneshvar\Survey\Models\Entry;

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
        if ($this->entry->lang == 'en') {
            $subject = 'SURVEY ' . $this->entry->survey->getTranslation('name', 'en');
            $viewNotification = 'survey::emails.survey-completed-message-en';
        } else {
            $subject = 'ENCUESTA ' . $this->entry->survey->getTranslation('name', 'es');
            $viewNotification = 'survey::emails.survey-completed-message-es';
        }
        return $this->subject($subject)
            ->from(config('iworking-survey.mail-from.address'), config('iworking-survey.mail-from.address'))
            ->view($viewNotification);
    }
}
