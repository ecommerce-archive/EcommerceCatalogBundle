<?php

namespace Ecommerce\Bundle\CatalogBundle\Product\Reference;

use Ecommerce\Bundle\CatalogBundle\Event\ProductEvent;
use Ecommerce\Bundle\CatalogBundle\Doctrine\Phpcr\ProductInterface;

class ReferenceLoader
{
    private $productReferenceRepository;

    /**
     * Constructor.
     */
    public function __construct($productReferenceRepository)
    {
        $this->productReferenceRepository = $productReferenceRepository;
    }



    public function postLoad(ProductEvent $event)
    {
        $product = $event->getProduct();

        if (!$product->getProductReference()) {
            $this->addProductReference($product);
        }
    }


    private function addProductReference(ProductInterface $product)
    {
        $productReference = $this->productReferenceRepository->findOrCreate($product);
        $product->setProductReference($productReference);
        $productReference->setProduct($product);
    }
}
