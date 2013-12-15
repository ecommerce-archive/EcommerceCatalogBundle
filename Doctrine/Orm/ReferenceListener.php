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
    }


    public function getSubscribedEvents()
    {
        return array(
            'postLoad',
        );
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof ProductReferenceInterface && !$args->getEntity()->getProduct()) {
            $this->addProduct($args->getEntity());
        }
    }


    private function addProduct(ProductReferenceInterface $productReference)
    {
        $productRepository = $this->container->get('ecommerce_catalog.persistence.phpcr.product.repository');
        $productReference->setProduct($productRepository->getReference($productReference->getId()));
    }
}
