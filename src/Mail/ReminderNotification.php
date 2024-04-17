<?php

namespace MattDaneshvar\Survey\Mail;

use DateTime;
use Illuminate\Mail\Mailable;
use MattDaneshvar\Survey\Models\Entry;
use MattDaneshvar\Survey\Models\Survey;
use Illuminate\Support\Facades\Crypt;


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
            setlocale(LC_ALL, 'english');
            $subject = 'Reminder survey B-Corp - Laboratorios Rubio';
            $viewNotification = 'survey::emails.reminders.reminder-en';
        } else {
            setlocale(LC_ALL, 'spanish');
            $subject = 'Recordatorio Formulario B-Corp - Laboratorios Rubio';
            $viewNotification = 'survey::emails.reminders.reminder-es';
        }

        $date       = strtotime($this->entry->survey->expiration);
        //Dia Fecha
        $day        = date('d', $date);
        //Nombre mes
        $dateObj   = DateTime::createFromFormat('!m', date('m', $date));
        $monthName = strftime('%B', $dateObj->getTimestamp());
        //url
        $mailCrypted =  Crypt::encryptString($this->entry->surveyed->email . ';' . $this->entry->survey->id);
        $url = config('iworking-survey.url') . '/survey/answers/' . $mailCrypted;

        return $this->subject($subject)
            ->from(config('iworking-survey.mail-from.address'), config('iworking-survey.mail-from.address'))
            ->view($viewNotification, [
                'day'       => $day,
                'monthName' => ucfirst($monthName),
                'url'       => $url
            ]);
    }
}
