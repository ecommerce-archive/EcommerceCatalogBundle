<?php

namespace Ecommerce\Bundle\CatalogBundle\Form;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Form;

/**
 * @author Philipp Wahala <philipp.wahala@gmail.com>
 */
class GroupedForm
{
    private $formBuilder;

    private $formGroups = array();

    private $currentGroup;

    private $form;

    /**
     * Constructor.
     *
     * @param FormBuilder $formBuilder
     */
    public function __construct(FormBuilder $formBuilder)
    {
        $this->formBuilder = $formBuilder;
    }

    /**
     * @param FormBuilder $formBuilder
     *
     * @return GroupedForm
     */
    public function setFormBuilder($formBuilder)
    {
        $this->formBuilder = $formBuilder;

        return $this;
    }

    /**
     * @return FormBuilder
     */
    public function getFormBuilder()
    {
        return $this->formBuilder;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        if (!$this->form) {
            $this->form = $this->getFormBuilder()->getForm();
        }

        return $this->form;
    }

    /**
     * @return GroupedForm
     */
    public function createView()
    {
        if (!$this->form) {
            $this->getForm();
        }

        $this->form = $this->form->createView();

        return $this;
    }



    /**
     * @param string $name
     *
     * @return GroupedForm
     */
    public function with($name)
    {
        if (!isset($this->formGroups[$name])) {
            $this->formGroups[$name] = array();
        }

        $this->currentGroup = $name;

        return $this;
    }

    /**
     * @return GroupedForm
     */
    public function end()
    {
        $this->currentGroup = null;

        return $this;
    }

    public function getGroups()
    {
        return $this->formGroups;
    }



    /**
     * {@inheritdoc}
     */
    public function add($child, $type = null, array $options = array())
    {
        $test = $this->formBuilder->add($child, $type, $options);

        if ($type === 'submit') {
            return $this;
        }

        if ($this->currentGroup) {
            $groupName = $this->currentGroup;
        } elseif (!empty($this->formGroups)) {
            $groupName = key($this->formGroups);
        } else {
            $groupName = 'General';
        }

        $this->formGroups[$groupName][] = $child;

        return $this;
    }
}
