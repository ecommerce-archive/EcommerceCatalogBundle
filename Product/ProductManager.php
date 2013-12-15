<?php

namespace Ecommerce\Bundle\CatalogBundle\Product;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\ODM\PHPCR\Document\Generic;

use PHPCR\ItemExistsException;

use Ecommerce\Bundle\CatalogBundle\Doctrine\Phpcr\Product;
use Ecommerce\Bundle\CatalogBundle\Doctrine\Phpcr\ProductInterface;
use Ecommerce\Bundle\CatalogBundle\Form\GroupedForm;
use Ecommerce\Bundle\CatalogBundle\Form\DataMapper\NodeDataMapper;

class ProductManager implements ContainerAwareInterface
{
    /** @var DocumentManager */
    private $dm;

    private $basepath;

    /** @var Generic */
    private $productBaseNode;


    private $products;


    /**
     * @var ContainerInterface
     */
    protected $container;

    protected $formBuilder;


    /**
     * Constructor.
     */
    public function __construct(DocumentManager $dm, $basepath)
    {
        $this->dm        = $dm;
        $this->basepath  = $basepath;
    }

    /**
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     */
    public function getCreateForm()
    {
        $class = $this->container->get('service_container')->getParameter('ecommerce_catalog.persistence.phpcr.product.class');

        $product = new $class(uniqid('__tmp'), $this->getProductBaseNode());

        $utx = $this->dm->getPhpcrSession()->getWorkspace()->getTransactionManager();
        if (!$utx->inTransaction()) {
            $utx->begin();
        }

        $this->dm->persist($product);
        $this->dm->flush();

        $this->formBuilder = $this->container->get('form.factory')->createNamedBuilder(
            'product_create',
            'form',
            $product,
            array(
                'data_class' => $class
            )
        );

//        $this->formBuilder->setDataMapper(new NodeDataMapper());

        $groupedForm = new GroupedForm($this->formBuilder);


        $groupedForm->add(
            $groupedForm->getFormBuilder()->create(
                'name',
                'text',
                array(
                    'property_path' => 'name',
                    'required'     => false,
                    'inherit_data' => true,
                )
            )->setDataMapper(new NodeDataMapper()),
            null,
            array(
                'inherit_data' => true,
            )
        );

        $groupedForm->add(
            'type',
            'text',
            array('required' => false,
            )
        );

        $properties = $this->container->get('ecommerce_catalog.product_properties_registry')->getProperties();

        foreach ($properties as $propertyName => $propertyOptions) {
//            $formGroup = $groupedForm->with('');
//            foreach ($properties as $propertyName => $propertyOptions) {
//            $groupedForm->add($propertyName, isset($propertyOptions['form_type']) ? $propertyOptions['form_type'] : null, isset($propertyOptions['form_options']) ? $propertyOptions['form_options'] : array());
//            }
//            $formGroup->end();
        }

//        $data = $this->container->get('ecommerce_catalog.product_properties_registry')->getProperties();
//
//        $formGroup = $groupedForm;
//        foreach ($data as $group => $properties) {
////            $formGroup = $groupedForm->with($group);
//            foreach ($properties as $propertyName => $propertyOptions) {
//                $groupedForm->add($propertyName, isset($propertyOptions['form_type']) ? $propertyOptions['form_type'] : null, isset($propertyOptions['form_options']) ? $propertyOptions['form_options'] : array());
//            }
////            $formGroup->end();
//        }

        return $groupedForm;
    }

    /**
     */
    public function getCreateForm2()
    {
        $class = $this->container->get('service_container')->getParameter('ecommerce_catalog.persistence.phpcr.product.class');

        $product = new $class(uniqid('__tmp'), $this->getProductBaseNode());

        $utx = $this->dm->getPhpcrSession()->getWorkspace()->getTransactionManager();
        if (!$utx->inTransaction()) {
            $utx->begin();
        }

        $this->dm->persist($product);
        $this->dm->flush();

        $formBuilder = $this->container->get('form.factory')->createNamedBuilder(
            'product_create',
            'form',
            $product,
            array(
                'data_class' => $class
            )
        );

//        $this->formBuilder->setDataMapper(new NodeDataMapper());

//        $groupedForm = new GroupedForm($this->formBuilder);


        $formBuilder->setDataMapper(new NodeDataMapper());

        $formBuilder->add(
            'name',
            'text',
            array(
                'required'      => false,
            )
        );

        $types = $this->container->get('ecommerce_catalog.product_types_registry')->getTypes();
        $typesArray = array();

        foreach ($types as $typeName => $typeOptions) {
            $typesArray[$typeName] = isset($typeOptions['name']) ? $typeOptions['name'] : $this->humanize($typeName);
        }

        if (count($typesArray) > 1) {

            $formBuilder->add(
                'type',
                'choice',
                array(
                    'required' => false,
                    'choices'  => $typesArray,
                )
            );
        } elseif (count($typesArray) === 1) {
            $formBuilder->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) use ($typesArray) {

                    $product = $event->getData();

                    $product->setType(key($typesArray));
                }
            );

        } else {
            throw new \RuntimeException('No product types defined');
        }

        return $formBuilder;

    }

    public function humanize($text)
    {
        return ucfirst(trim(strtolower(preg_replace(array('/([A-Z])/', '/[_\s]+/'), array('_$1', ' '), $text))));
    }

    /**
     */
    public function getEditForm(ProductInterface $product)
    {
        $formBuilder = $this->container->get('form.factory')->createNamedBuilder(
            'product_edit',
            'form',
            $product,
            array(
                'data_class' => $this->container->get('service_container')->getParameter('ecommerce_catalog.persistence.phpcr.product.class')
            )
        );

        $formBuilder->setDataMapper(new NodeDataMapper());

        $types = $this->container->get('ecommerce_catalog.product_types_registry')->getTypes();
        $typeProperties = $this->container->get('ecommerce_catalog.product_types_registry')->getTypeProperties($product->getType());



//        $groupedForm = new GroupedForm($this->formBuilder);

//        $product->get

//        $typeProperties = isset($type['properties']) && is_array($type['properties']) ? $type['properties'] : array();
        $properties = $this->container->get('ecommerce_catalog.product_properties_registry')->getProperties();

        foreach ($typeProperties as $property) {
//            $formGroup = $groupedForm->with('');
//            foreach ($properties as $propertyName => $propertyOptions) {
            $formBuilder->add($property, isset($properties[$property]['form_type']) ? $properties[$property]['form_type'] : null, isset($properties[$property]['form_options']) ? $properties[$property]['form_options'] : array());
//            }
//            $formGroup->end();
        }

//        foreach ($properties as $propertyName => $propertyOptions) {
//            $formGroup = $groupedForm->with('');
//            foreach ($properties as $propertyName => $propertyOptions) {
//            $groupedForm->add($propertyName, isset($propertyOptions['form_type']) ? $propertyOptions['form_type'] : null, isset($propertyOptions['form_options']) ? $propertyOptions['form_options'] : array());
//            }
//            $formGroup->end();
//        }

        return $formBuilder;
    }

    public function getProductFormGroups($type)
    {
        return $this->container->get('ecommerce_catalog.product_types_registry')->getTypePropertyGroups($type);
    }


    public function save(ProductInterface $product = null)
    {
        try {
            if ($product) {
                $product->setUpdatedAt();
                $this->dm->persist($product);
            }
            $this->dm->flush();

            $utx = $this->dm->getPhpcrSession()->getWorkspace()->getTransactionManager();
            if ($utx->inTransaction()) {
                $utx->commit();
            }

            return true;
        } catch (ItemExistsException $e) {
            return false;
        }
    }


    public function commit()
    {
        $utx = $this->dm->getPhpcrSession()->getWorkspace()->getTransactionManager();
        if ($utx->inTransaction()) {
            $utx->commit();
        }
    }


    public function rollback()
    {
        $utx = $this->dm->getPhpcrSession()->getWorkspace()->getTransactionManager();
        if ($utx->inTransaction()) {
            $utx->rollback();
        }
    }


    public function delete(ProductInterface $product)
    {
        try {
            $this->dm->remove($product);
            $this->dm->flush();

            return true;
        } catch (ItemExistsException $e) {
            return false;
        }
    }



    /**
     * @param string $id
     * @return Product|null
     */
    public function find($id)
    {
        return $this->dm->find(null, $id);
    }

    /**
     * @param string $id
     * @return Product|null
     */
    public function findByName($id)
    {
        return $this->dm->find(null, $this->basepath.'/'.$id);
    }

    /**
     * @param bool $reload
     * @return Product[]
     */
    public function findAll($reload = false)
    {
        if ($this->products !== null & !$reload) {

            return $this->products;
        }

        return $this->products = $this->getProductBaseNode()->getChildren();
    }




    /**
     * @return Generic
     */
    public function getProductBaseNode()
    {
        if ($this->productBaseNode !== null) {
            return $this->productBaseNode;
        }

        return $this->productBaseNode = $this->dm->find(null, $this->basepath);
    }

}
