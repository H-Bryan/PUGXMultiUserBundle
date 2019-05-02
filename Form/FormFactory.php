<?php

namespace PUGX\MultiUserBundle\Form;

use FOS\UserBundle\Form\Factory\FactoryInterface;
use PUGX\MultiUserBundle\Model\UserDiscriminator;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Kernel;

class FormFactory implements FactoryInterface
{
    /** @var UserDiscriminator */
    private $userDiscriminator;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var string */
    private $type;

    /** @var array */
    private $forms = [];

    /**
     * @param UserDiscriminator    $userDiscriminator
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(UserDiscriminator $userDiscriminator, FormFactoryInterface $formFactory)
    {
        $this->userDiscriminator = $userDiscriminator;
        $this->formFactory = $formFactory;
    }

    /**
     * @return Form
     */
    public function createForm()
    {
        $type = $this->userDiscriminator->getFormType($this->type);
        $name = $this->userDiscriminator->getFormName($this->type);
        $validationGroups = $this->userDiscriminator->getFormValidationGroups($this->type);

        if (array_key_exists($name, $this->forms)) {
            return $this->forms[$name];
        }

        if (Kernel::MAJOR_VERSION >= 3) {
            $form = $this->formFactory->createNamed(
                $name,
                get_class($type),
                null,
                ['validation_groups' => $validationGroups]
            );
        } else {
            // Legacy support
            $form = $this->formFactory->createNamed(
                $name,
                $type,
                null,
                ['validation_groups' => $validationGroups]
            );
        }

        $this->forms[$name] = $form;

        return $form;
    }

    /**
     * @param string $type
     * @return FormFactory
     */
    public function setType(string $type): FormFactory
    {
        $this->type = $type;

        return $this;
    }
}
