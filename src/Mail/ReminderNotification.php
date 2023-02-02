<?php

namespace MattDaneshvar\Survey\Mail;

use Illuminate\Mail\Mailable;
use MattDaneshvar\Survey\Models\Entry;
use MattDaneshvar\Survey\Models\Survey;


class ReminderNotification extends Mailable
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
            $subject = 'Recordatorio en ingles';
            $viewNotification = 'survey::emails.reminders.reminder-en';
        } else {
            $subject = 'Recordatorio en español';
            $viewNotification = 'survey::emails.reminders.reminder-es';
        }
        return $this->subject($subject)
            ->from(config('iworking-survey.mail-from.address'), config('iworking-survey.mail-from.address'))
            ->view($viewNotification);
    }
}
