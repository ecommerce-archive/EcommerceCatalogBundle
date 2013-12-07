<?php

namespace Ecommerce\Bundle\CatalogBundle\Form\DataMapper;

use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

use Jackalope\Node;

/**
 * A data mapper to read/write data to a Jackalope node.
 * The node has to be accessible via the getNode method
 *
 * @author Philipp Wahala <philipp.wahala@gmail.com>
 */
class NodeDataMapper implements DataMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function mapDataToForms($data, $forms)
    {
        $empty = null === $data || array() === $data;

        if (!$empty && !is_array($data) && !is_object($data)) {
            throw new UnexpectedTypeException($data, 'object, array or empty');
        }

        $node = $data->getNode();

        if (!$node instanceof Node) {
            return;
            throw new UnexpectedTypeException($data, 'Jackalope\\Node');
        }

        foreach ($forms as $form) {
            $propertyPath = $form->getPropertyPath();
            $config = $form->getConfig();

            if (!$config->getMapped()) {
                continue;
            }

            if ($config->getOption('translate_field', false) && null !== $propertyPath) {
                $form->setData($data->getTranslatedProperty(strval($propertyPath)));
            } elseif (null !== $propertyPath) {
                $form->setData($node->getPropertyValueWithDefault(strval($propertyPath), null));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function mapFormsToData($forms, &$data)
    {
        if (null === $data) {
            return;
        }

        if (!is_array($data) && !is_object($data)) {
            throw new UnexpectedTypeException($data, 'object, array or empty');
        }

        $node = $data->getNode();

        if (!$node instanceof Node) {
            throw new UnexpectedTypeException($data, 'Jackalope\\Node');
        }

        foreach ($forms as $form) {
            $propertyPath = $form->getPropertyPath();
            $config = $form->getConfig();

            if (null !== $propertyPath && $config->getMapped() && $form->isSynchronized() && !$form->isDisabled()) {


                // @TODO: event with $formData = $form->getData();?

                if ($config->getOption('translate_field', false) && is_array($form->getData())) {
                    $data->setTranslatedProperty(strval($propertyPath), $form->getData());
                } elseif ($config->getOption('image_path', false)) {
                    if ($form->getData() !== null) {
                        $node->setProperty(strval($propertyPath), $form->getData());
                    }
                } elseif (!is_object($data) || !$config->getByReference() || $form->getData() !== $node->getPropertyValueWithDefault(strval($propertyPath), null)) {
                    $node->setProperty(strval($propertyPath), $form->getData());
                }
            }
        }
    }
}
