<?php

namespace Ecommerce\Bundle\CatalogBundle\Doctrine\Orm;

use Doctrine\ORM\EntityRepository;

use Ecommerce\Bundle\CatalogBundle\Doctrine\Phpcr\Product;

class ProductReferenceRepository extends EntityRepository
{
    public function create(Product $product)
    {
        $productReference = new ProductReference($product->getIdentifier(), $product->getName());

        $this->_em->persist($productReference);
        $this->_em->flush();

        return $productReference;
    }

    public function findOrCreate(Product $product)
    {
        $productReference = $this->find($product->getIdentifier());

        if ($productReference) {
            return $productReference;
        }

        // @TODO: Log as this shouldn’t happen
        return $this->create($product);
    }

    public function delete(Product $product)
    {
        $productReference = $this->find($product->getIdentifier());

        if (!$productReference) {
            return false;
        }


        $this->_em->remove($productReference);
        $this->_em->flush();

        return true;
    }

    public function getReference($productId)
    {
        $productReference = $this->_em->getReference($this->getClassName(), $productId);

        return $productReference;
    }
}
