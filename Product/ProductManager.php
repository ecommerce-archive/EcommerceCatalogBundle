<?php

namespace Ecommerce\Bundle\CatalogBundle\Product;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

        $this->formBuilder->setDataMapper(new NodeDataMapper());

        $groupedForm = new GroupedForm($this->formBuilder);

        $data = $this->container->get('ecommerce_catalog.product_properties_registry')->getProperties();

        foreach ($data as $group => $properties) {
            $formGroup = $groupedForm->with($group);
            foreach ($properties as $propertyName => $propertyOptions) {
                $formGroup->add($propertyName, $propertyOptions['form_type'], isset($propertyOptions['form_options']) ? $propertyOptions['form_options'] : array());
            }
            $formGroup->end();
        }

        return $groupedForm;
    }

    /**
     */
    public function getEditForm(ProductInterface $product)
    {
        $this->formBuilder = $this->container->get('form.factory')->createNamedBuilder(
            'product_edit',
            'form',
            $product,
            array(
                'data_class' => $this->container->get('service_container')->getParameter('ecommerce_catalog.persistence.phpcr.product.class')
            )
        );

        $this->formBuilder->setDataMapper(new NodeDataMapper());

        $groupedForm = new GroupedForm($this->formBuilder);

        $data = $this->container->get('ecommerce_catalog.product_properties_registry')->getProperties();

        foreach ($data as $group => $properties) {
            $formGroup = $groupedForm->with($group);
            foreach ($properties as $propertyName => $propertyOptions) {
                $formGroup->add($propertyName, $propertyOptions['form_type'], isset($propertyOptions['form_options']) ? $propertyOptions['form_options'] : array());
            }
            $formGroup->end();
        }

        return $groupedForm;
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

        return $this->products = $this->getProductNode()->getChildren();
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
