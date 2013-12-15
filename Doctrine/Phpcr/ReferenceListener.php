<?php

namespace Ecommerce\Bundle\CatalogBundle\Doctrine\Phpcr;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

use Ecommerce\Bundle\CatalogBundle\Event\EcommerceCatalogEvents;
use Ecommerce\Bundle\CatalogBundle\Event\ProductEvent;

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
    }


    public function getSubscribedEvents()
    {
        return array(
            'postLoad',
//            'prePersist',
            'postPersist',
            'preUpdate',
            'postUpdate',
            'preRemove',
            'postRemove',
        );
    }




    public function postLoad(LifecycleEventArgs $args)
    {
        if ($args->getObject() instanceof ProductInterface) {
            $event = new ProductEvent($args->getObject());
            $this->eventDispatcher->dispatch(EcommerceCatalogEvents::PRODUCT_POST_LOAD, $event);
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

            $this->eventDispatcher->dispatch(EcommerceCatalogEvents::PRODUCT_PRE_UPDATE);
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

            $event = new ProductEvent($document);
            $this->eventDispatcher->dispatch(EcommerceCatalogEvents::PRODUCT_POST_UPDATE, $event);
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
