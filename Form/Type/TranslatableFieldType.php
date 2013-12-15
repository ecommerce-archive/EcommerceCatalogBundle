<?php

namespace Ecommerce\Bundle\CatalogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;


class TranslatableFieldType extends AbstractType
{
    protected $translator;

    protected $locales;

    /**
     * @param TranslatorInterface $translator
     * @param array               $locales
     */
    public function __construct(TranslatorInterface $translator, array $locales)
    {
        $this->translator = $translator;
        $this->locales = $locales;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->locales as $locale => $fallbacks) {
            $builder->add($locale, $options['field_type'], array_merge(array('required' => false), $options['field_options']));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'translatable_field';
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'field_type'         => 'text',
            'field_options'      => array(),
            'translatable_field' => true,
        ));
    }
}
