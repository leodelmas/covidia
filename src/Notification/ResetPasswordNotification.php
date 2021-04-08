<?php

namespace App\Notification;

use Swift_Mailer;
use Swift_Message;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ResetPasswordNotification {

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
     * @param ResetPasswordToken $resetToken
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function notify(string $email, ResetPasswordToken $resetToken): int
    {
        $resetPasswordMessage = (new Swift_Message("Covidia : Réinitialisation du mdp"))
            ->setFrom('noreply@covidia.fr')
            ->setTo($email)
            ->setBody($this->renderer->render('emails/reset_password.html.twig', [
                'resetToken' => $resetToken
            ]), 'text/html');

        return $this->mailer->send($resetPasswordMessage);
    }
}