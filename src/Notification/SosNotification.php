<?php

namespace App\Notification;

use App\Entity\Sos;
use App\Entity\User;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class SosNotification {

    /**
     * @var Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $renderer;

    /**
     * @var string
     */
    private $psychologistMail = "psy@covidia.com";

    /**
     * SosNotification constructor.
     * @param Swift_Mailer $mailer
     * @param Environment $renderer
     */
    public function __construct(Swift_Mailer $mailer, Environment $renderer) {
        $this->mailer = $mailer;
        $this->renderer = $renderer;

    }

    /**
     * @param Sos $sos
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function notify(Sos $sos, array $psychologists) {
        foreach ($psychologists as $psychologist){
            $psychologistMessage = (new Swift_Message("Covidia : SOS psychologue"))
            ->setFrom('noreply@covidia.xyz')
            ->setTo($psychologist->getEmail())
            ->setBody($this->renderer->render('emails/sos/psychologist.html.twig', [
                'sos' => $sos
            ]), 'text/html');
            $this->mailer->send($psychologistMessage);
        }
      
        $userMessage = (new Swift_Message("Covidia : SOS psychologue"))
            ->setFrom('noreply@covidia.xyz')
            ->setTo($sos->getUser()->getEmail())
            ->setBody($this->renderer->render('emails/sos/user.html.twig', [
                'sos' => $sos
            ]), 'text/html');

        
        $this->mailer->send($userMessage);
    }
}