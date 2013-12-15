<?php

namespace Ecommerce\Bundle\CatalogBundle\Event;

use Symfony\Component\EventDispatcher\Event;

use Ecommerce\Bundle\CatalogBundle\Doctrine\Phpcr\ProductInterface;

/**
 * @author Philipp Wahala <philipp.wahala@gmail.com>
 */
class ProductEvent extends Event
{
    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * Constructor.
     *
     * @param ProductInterface $product
     */
    public function __construct(ProductInterface $product)
    {
        $this->product = $product;
    }

    public function getProduct()
    {
        return $this->product;
    }
}
