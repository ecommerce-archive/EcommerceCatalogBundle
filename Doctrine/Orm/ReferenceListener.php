<?php

namespace Ecommerce\Bundle\CatalogBundle\Doctrine\Orm;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;

use Ecommerce\Bundle\CatalogBundle\Doctrine\Phpcr\ReferenceListener as OtherReferenceListener;

class ReferenceListener implements EventSubscriber, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

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

        return $this->otherReferenceListener = $this->container->get('ecommerce_catalog.persistence.phpcr.reference_listener');
    }

    public function isOtherListenerIsNotActive()
    {
        return !$this->getOtherReferenceListener()->isActive();
    }


    public function postLoad(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof ProductReferenceInterface && $this->isOtherListenerIsNotActive()) {
            $this->setActive();
            $this->addProduct($args->getEntity());
            $this->setNotActive();
        }
    }


    private function addProduct(ProductReferenceInterface $productReference)
    {
        $productRepository = $this->container->get('ecommerce_catalog.persistence.phpcr.product.repository');
        $productReference->setProduct($productRepository->getReference($productReference->getId()));
    }
}
