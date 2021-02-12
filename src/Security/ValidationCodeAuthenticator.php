<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class ValidationCodeAuthenticator extends AbstractGuardAuthenticator
{
    use TargetPathTrait;
    public const LOGIN_ROUTE = 'validation';
    public const USER_SESSION_KEY = 'auth_user';
    public const CODE_SESSION_KEY = 'auth_code';
    public const TIMEOUT_SESSION_KEY = 'auth_timeout';
    public const COUNT_SESSION_KEY = 'auth_count';
    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $session;

    /**
     * ValidationCodeAuthenticator constructor.
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param SessionInterface $session
     */
    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, SessionInterface $session) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->session = $session;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool {
        return self::LOGIN_ROUTE === $request->attributes->get('_route') && $request->isMethod('POST');
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getCredentials(Request $request): array {
        return [
            'email' => $request->getSession()->get(self::USER_SESSION_KEY),
            'count' => $request->getSession()->get(self::COUNT_SESSION_KEY, 1),
            'timeout' => $request->getSession()->get(self::TIMEOUT_SESSION_KEY),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return object|UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider) {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }
        if (time() > $credentials['timeout']) {
            throw new CustomUserMessageAuthenticationException('Temps pour valider dépassé.');
        }
        if ($credentials['count'] >= 3) {
            throw new CustomUserMessageAuthenticationException('Nombre de tentatives de connexion dépassé.');
        }
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);
        if (!$user) {
            throw new CustomUserMessageAuthenticationException('E-mail non trouvé.');
        }
        return $user;
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool {
        if ($credentials['password'] === $this->session->get(self::CODE_SESSION_KEY, null)) {
            return true;
        }
        $this->session->set(self::COUNT_SESSION_KEY, $credentials['count'] + 1);
        return false;
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return RedirectResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse {
        // TODO: envoie message erreur
        return new RedirectResponse('/validation');
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): RedirectResponse {
            $request->getSession()->remove('need_auth_two');
            $request->getSession()->remove(self::USER_SESSION_KEY);
            $request->getSession()->remove(self::CODE_SESSION_KEY);
            $request->getSession()->remove(self::TIMEOUT_SESSION_KEY);
            $request->getSession()->remove(self::COUNT_SESSION_KEY);
            if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
                return new RedirectResponse($targetPath);
            }
            return new RedirectResponse('/');
    }

    /**
     * @return string
     */
    protected function getLoginUrl(): string {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    /**
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null): Response {
        //TODO
   }

    /**
     * @return false
     */
    public function supportsRememberMe(): bool {
        return false;
    }
}
