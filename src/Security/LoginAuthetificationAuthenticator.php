<?php
namespace App\Security;

use App\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\EntityManagerInterface;

class LoginAuthetificationAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private RouterInterface $router,
        private EntityManagerInterface $entityManager
    ) {}

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');
        $csrfToken = $request->request->get('_csrf_token', '');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        // Load the user from the database
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

        // Check if the user is blocked
        if ($user && $user->isBlocked()) {
            throw new CustomUserMessageAuthenticationException('Your account is blocked. Please contact support.');
        }

        return new Passport(
            new UserBadge($email, function ($email) {
                return $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
            }),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Redirect to the target path if it exists
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Get the user's roles
        $roles = $token->getRoleNames();

        // Redirect based on roles
        if (in_array('ROLE_admin', $roles)) {
            return new RedirectResponse($this->router->generate('admin_dashboard'));
        }

        // Default redirect after login
        return new RedirectResponse($this->router->generate('app_home')); // Replace 'app_home' with your default route
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}