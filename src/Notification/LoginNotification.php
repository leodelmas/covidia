<?php

namespace App\Notification;

use Swift_Mailer;
use Swift_Message;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class LoginNotification {

    private $mailer;
    private $renderer;

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
     * @param string $email
     * @param string $code
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function notify(string $email, string $code) {
        $loginMessage = (new Swift_Message("Covidia : Nouvelle connexion"))
            ->setFrom('noreply@covidia.fr')
            ->setTo($email)
            ->setBody($this->renderer->render('emails/login.html.twig', [
                'code' => $code
            ]), 'text/html');

        $this->mailer->send($loginMessage);
    }
}