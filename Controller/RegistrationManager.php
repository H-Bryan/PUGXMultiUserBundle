<?php

namespace PUGX\MultiUserBundle\Controller;

use PUGX\MultiUserBundle\Model\UserDiscriminator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use FOS\UserBundle\Controller\RegistrationController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PUGX\MultiUserBundle\Form\FormFactory;

class RegistrationManager
{
    /**
     * @var UserDiscriminator
     */
    protected $userDiscriminator;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RegistrationController
     */
    protected $controller;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @param UserDiscriminator $userDiscriminator
     * @param ContainerInterface $container
     * @param RegistrationController $controller
     * @param FormFactory $formFactory
     */
    public function __construct(UserDiscriminator $userDiscriminator,
                                ContainerInterface $container,
                                RegistrationController $controller,
                                FormFactory $formFactory)
    {
        $this->userDiscriminator = $userDiscriminator;
        $this->container = $container;
        $this->controller = $controller;
        $this->formFactory = $formFactory;
    }

    /**
     * @param string $class
     *
     * @return Response
     */
    public function register($class)
    {
        $this->userDiscriminator->setClass($class);
        $this->controller->setContainer($this->container);
        $result = $this->controller->registerAction($this->getRequest());
        if ($result instanceof Response) {
            return $result;
        }

        $template = $this->userDiscriminator->getTemplate('registration');
        if (is_null($template)) {
            $template = 'FOSUserBundle:Registration:register.html.twig';
        }

        $form = $this->formFactory->setType('registration')->createForm();

        return $this->container->get('templating')->renderResponse($template, [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return Request
     */
    private function getRequest()
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }
}
