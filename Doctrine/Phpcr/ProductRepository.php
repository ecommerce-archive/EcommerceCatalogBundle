<?php

namespace Ecommerce\Bundle\CatalogBundle\Doctrine\Phpcr;

use Doctrine\ODM\PHPCR\DocumentRepository;
use Jackalope\Node;

class ProductRepository extends DocumentRepository
{
    /**
     * @param string $uuid
     *
     * @return Product
     */
    public function getReference($uuid)
    {
        $node = $this->getDocumentManager()->getPhpcrSession()->getObjectManager()->getCachedNodeByUuid($uuid);
        if ($node instanceof Node && ($product = $this->getDocumentManager()->find(null, $node->getPath()))) {
            return $product;
        }
            $node = $this->getDocumentManager()->getPhpcrSession()->getNodeByIdentifier($uuid);

        return $this->dm->getReference($this->getClassName(), $node->getPath());

        $productReference = $this->dm->getReference($this->getClassName(), $path);

        return $productReference;
    }
}
