<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

// Injection Classes
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;

class ExceptionListener
{

	protected $serviceContainer;
	protected $templating;
	
	public function __construct(
		ContainerInterface $serviceContainer,
		TwigEngine $templating
	){
		$this->serviceContainer = $serviceContainer;
		$this->templating = $templating;
	}

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
		
		$response = new Response();
		
        $exception = $event->getException();
        $message = sprintf(
            'Your Error is: %s with code: %s',
            $exception->getMessage(),
            $exception->getCode()
        );

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        
        $template = $this->templating->render('@AppBundle/Resources/views/Common/exception.html.twig', array(
	        'message' => $message
        ));
        
        $response->setContent($template);
	    $event->setResponse($response);
	    
    }
}
