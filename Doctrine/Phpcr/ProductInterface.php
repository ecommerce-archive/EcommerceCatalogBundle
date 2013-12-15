<?php

namespace Ecommerce\Bundle\CatalogBundle\Doctrine\Phpcr;

interface ProductInterface
{
    const STATUS_CREATED = 0;
    const STATUS_DRAFT = 1;
    const STATUS_PUBLISHED = 2;
    const STATUS_UNPUBLISHED = 3;

    public function getId();

    public function getIdentifier();

    public function getName();

    public function getType();

    public function all();

    public function getProperty($name);

    public function get($name, $default = null);

    public function hasProperty($name);

    public function set($name, $value);

}
