<?php

namespace PUGX\MultiUserBundle\Controller;

use FOS\UserBundle\Controller\ProfileController;
use PUGX\MultiUserBundle\Form\FormFactory;
use PUGX\MultiUserBundle\Model\UserDiscriminator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProfileManager
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
     * @var ProfileController
     */
    protected $controller;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @param UserDiscriminator  $userDiscriminator
     * @param ContainerInterface $container
     * @param ProfileController  $controller
     * @param FormFactory        $formFactory
     */
    public function __construct(
        UserDiscriminator $userDiscriminator,
        ContainerInterface $container,
        ProfileController $controller,
        FormFactory $formFactory
    ) {
        $this->userDiscriminator = $userDiscriminator;
        $this->container = $container;
        $this->controller = $controller;
        $this->formFactory = $formFactory;
    }

    /**
     * @param string $class
     *
     * @return RedirectResponse
     */
    public function edit($class)
    {
        $this->userDiscriminator->setClass($class);

        $this->controller->setContainer($this->container);
        $result = $this->controller->editAction($this->getRequest());
        if ($result instanceof RedirectResponse) {
            $result;
        }

        $template = $this->userDiscriminator->getTemplate('profile');
        if (is_null($template)) {
            $template = 'FOSUserBundle:Profile:edit.html.twig';
        }

        $form = $this->formFactory->setType("profile")->createForm();

        return $this->container->get('templating')->renderResponse($template, [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request;
     */
    private function getRequest()
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }
}
