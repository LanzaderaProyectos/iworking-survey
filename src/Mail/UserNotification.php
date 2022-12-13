<?php

namespace MattDaneshvar\Survey\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Queue\SerializesModels;
use MattDaneshvar\Survey\Models\Survey;
use Barryvdh\DomPDF\Facade as PDF;


class UserNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Order
     */
    public Survey $survey;

    /**
     * @var string
     */
    public $emailUser;

    public $user;

    /**
     * Create a new message instance.
     * @param Survey $order
     * @param $emailProvider
     */
    public function __construct(Survey $survey, $user)
    {
        $this->user = $user;
        $this->survey   = $survey;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        if ($this->user->lang == 'en') {
            $subject = 'Important: Supplier Best Practices.';
            $viewNotification = 'survey::emails.survey-notification-en';
        } else {
            $subject = 'Importante: Buenas prácticas de proveedores.';
            $viewNotification = 'survey::emails.survey-notification-es';
        }
        // $pdf = PDF::loadView('survey::exports.pdf-entry');
        $mailCrypted =  Crypt::encryptString($this->user->email . ';' . $this->survey->id);
        $url = config('iworking-survey.url') . '/survey/answers/' . $mailCrypted;
        return $this->subject($subject)
            ->from('mejorespracticas@labrubio.com', 'Lab Rubio')
            ->view($viewNotification, [
                'url'       => $url
            ]);
    }
}
