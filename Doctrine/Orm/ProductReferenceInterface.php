<?php

namespace Ecommerce\Bundle\CatalogBundle\Doctrine\Orm;

use Ecommerce\Bundle\CatalogBundle\Doctrine\Phpcr\Product;

class ProductReferenceInterface
{
    public function getProduct();
    public function setProduct($product);
}
