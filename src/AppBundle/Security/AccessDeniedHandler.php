<?php

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{

    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $url = $this->router->generate('fos_user_security_login');
	    return new RedirectResponse($url);
    }
}
