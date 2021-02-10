<?php

namespace App\Controller;

use App\Notification\LoginNotification;
use App\Security\ValidationCodeAuthenticator;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ValidationCodeController extends AbstractController {

    private $notification;

    /**
     * ValidationCodeController constructor.
     * @param LoginNotification $notification
     */
    public function __construct(LoginNotification $notification) {
        $this->notification = $notification;
    }

    /**
     * @Route("/validation", name="validation")
     * @param SessionInterface $session
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function twoFactor(SessionInterface $session,  AuthenticationUtils $authenticationUtils): Response {
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($session->get(ValidationCodeAuthenticator::CODE_SESSION_KEY) === null) {
            $error = null;
            $session->set(ValidationCodeAuthenticator::CODE_SESSION_KEY, $this->generateRandomCode());
            $session->set(ValidationCodeAuthenticator::TIMEOUT_SESSION_KEY, time() + (60 * 5));
            $session->set(ValidationCodeAuthenticator::COUNT_SESSION_KEY, 1);

            try {
                $this->notification->notify($session->get(ValidationCodeAuthenticator::USER_SESSION_KEY), $session->get(ValidationCodeAuthenticator::CODE_SESSION_KEY));
            }
            catch (LoaderError | RuntimeError | SyntaxError $e) {}
        }
        return $this->render('security/validation.html.twig', ['error' => $error]);
    }

    /**
     * @return string
     */
    private function generateRandomCode(): string {
        try {
            return (string)random_int(1000, 9999);
        }
        catch (Exception $e) {}
    }
}
