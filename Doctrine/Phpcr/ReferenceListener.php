<?php

namespace Ecommerce\Bundle\CatalogBundle\Doctrine\Phpcr;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

use Ecommerce\Bundle\CatalogBundle\Doctrine\Orm\ReferenceListener as OtherReferenceListener;

class ReferenceListener implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var OtherReferenceListener
     */
    protected $otherReferenceListener;

    protected $active;

    /**
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->eventDispatcher = $dispatcher;
    }


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->active = false;
    }


    public function getSubscribedEvents()
    {
        return array(
            'postLoad',
            'postPersist',
            'postUpdate',
            'preUpdate',
            'preRemove',
        );
    }

    public function isActive()
    {
        return $this->active;
    }

    public function setActive()
    {
        $this->active = true;
    }

    public function setNotActive()
    {
        $this->active = false;
    }

    public function getOtherReferenceListener()
    {
        if ($this->otherReferenceListener) {
            return $this->otherReferenceListener;
        }

        return $this->otherReferenceListener = $this->container->get('ecommerce_catalog.persistence.orm.reference_listener');
    }

    public function isOtherListenerIsNotActive()
    {
        return !$this->getOtherReferenceListener()->isActive();
    }



    public function postLoad(LifecycleEventArgs $args)
    {
        if ($args->getObject() instanceof ProductInterface) {
            $this->setActive();
            $this->addProductReference($args->getObject());
            $this->setNotActive();
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $document = $args->getObject();

        if ($document instanceof ProductInterface && (substr($document->getName(), 0, 5) !== '__tmp')) {

            $this->container->get('ecommerce_catalog.product_reference.repository')->create($document);
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $document = $args->getObject();

        if ($document instanceof ProductInterface) {
            $stop = $document->getNode()->getState();
            if ($document->getStatus() === ProductInterface::STATUS_CREATED) {
                $document->setStatus(ProductInterface::STATUS_DRAFT);
            }

            $this->eventDispatcher->dispatch('ecommmerce_catalog_product_update');
            $this->container->get('ecommerce_catalog.product_reference.repository')->findOrCreate($document);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $document = $args->getObject();

        if ($document instanceof ProductInterface) {
            $stop = $document->getNode()->getState();
            if ($document->getStatus() === ProductInterface::STATUS_CREATED) {
                $document->setStatus(ProductInterface::STATUS_DRAFT);
            }

            $this->eventDispatcher->dispatch('ecommmerce_catalog_product_update');
            $this->container->get('ecommerce_catalog.product_reference.repository')->findOrCreate($document);
        }
    }


    public function preRemove(LifecycleEventArgs $args)
    {
        $document = $args->getObject();

        if ($document instanceof ProductInterface) {
            $this->container->get('ecommerce_catalog.product_reference.repository')->delete($document);
        }
    }


    private function addProductReference(ProductInterface $product)
    {
        $productReference = $this->container->get('ecommerce_catalog.product_reference.repository')->findOrCreate($product);
        $product->setProductReference($productReference);
        $productReference->setProduct($product);
    }
}
