<?php

namespace Ecommerce\Bundle\CatalogBundle\Doctrine\Orm;

use Ecommerce\Bundle\CatalogBundle\Doctrine\Phpcr\Product;

class ProductReference implements ProductReferenceInterface
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var Product */
    private $product;


    /**
     * Constructor.
     *
     * @param string      $id
     * @param string|null $name
     */
    public function __construct($id, $name = null)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /** @return string */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return ProductReference
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /** @return string */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @param Product $product
     * @return $this
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }


}
