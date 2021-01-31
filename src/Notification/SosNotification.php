<?php

namespace App\Notification;

use App\Entity\Sos;
use App\Entity\User;
use Twig\Environment;

class SosNotification {

    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $renderer;

    public function __construct(\Swift_Mailer $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }

    public function notify(Sos $sos){
        $message = (new \Swift_Message($sos->getSujet()))
            ->setFrom('noreply@covidia.fr')
            ->setTo('psycoco@covidia.fr')
            ->setBody(
                $this->renderView(
                    get_Modele_Email($sos->getEmail(), $sos->getUser())
                ),
                'text/html'
            );

        $this->mailer->send($message);

        //Envoie de la copie à l'utilisateur
        $message->setTo($sos->getUser()->getEmail());

        $this->mailer->send($message);
    }

    private function get_Modele_Email(integer $email, User $user){
        switch($email){
            case 0:
                return $this->rendererView(
                    'emails/email1.html.twig',[
                        'firstName' => $user->getFirstname(),
                        'lastName' => $user->getLastname()
                    ]
                );
                break;
            case 1:
                return $this->rendererView(
                    'emails/email2.html.twig'
                );
                break;
            default:
                break;
        }
    }

}
?>