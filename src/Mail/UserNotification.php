<?php

namespace MattDaneshvar\Survey\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Queue\SerializesModels;
use MattDaneshvar\Survey\Models\Survey;

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
        if ($this->user->idioma == 'en') {
            $subject = 'SURVEY ' . $this->survey->getTranslation('name', 'en');
            $viewNotification = 'survey::emails.survey-notification-en';
        } else {
            $subject = 'ENCUESTA ' . $this->survey->getTranslation('name', 'es');
            $viewNotification = 'survey::emails.survey-notification-es';
        }
        $mailCrypted =  Crypt::encryptString($this->user->email . ';' . $this->survey->id);
        $url = config('iworking-survey.url') . '/survey/answers/' . $mailCrypted;
        return $this->subject($subject)
            ->from(config('iworking-survey.mail-from.address'), config('iworking-survey.mail-from.address'))
            ->view($viewNotification, [
                'user'      => $this->user,
                'survey'    => $this->survey,
                'url'       => $url
            ]);
    }
}
