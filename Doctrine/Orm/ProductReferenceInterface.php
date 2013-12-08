<?php

namespace Ecommerce\Bundle\CatalogBundle\Doctrine\Orm;

use Ecommerce\Bundle\CatalogBundle\Doctrine\Phpcr\ProductInterface;

interface ProductReferenceInterface
{
    public function getProduct();
    public function setProduct(ProductInterface $product);
}
