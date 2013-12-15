<?php

namespace Ecommerce\Bundle\CatalogBundle\Event;

/**
 * @author Philipp Wahala <philipp.wahala@gmail.com>
 */
final class EcommerceCatalogEvents
{
    const PRODUCT_POST_LOAD = 'ecommerce_catalog.product.post_load';

    const PRODUCT_PRE_PERSIST = 'ecommerce_catalog.product.pre_persist';

    const PRODUCT_POST_PERSIST = 'ecommerce_catalog.product.post_persist';

    const PRODUCT_PRE_UPDATE = 'ecommerce_catalog.product.pre_update';

    const PRODUCT_POST_UPDATE = 'ecommerce_catalog.product.post_update';

    const PRODUCT_PRE_REMOVE = 'ecommerce_catalog.product.pre_remove';

    const PRODUCT_POST_REMOVE = 'ecommerce_catalog.product.post_remove';
}
