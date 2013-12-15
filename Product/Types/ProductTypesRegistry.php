<?php

namespace Ecommerce\Bundle\CatalogBundle\Product\Types;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

class ProductTypesRegistry implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    private $files;

    /**
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function addFiles(array $files = array())
    {
        $this->files = $files;
    }

    public function getFiles()
    {
        return (array)$this->files;
    }


    /**
     * @return array
     */
    public function loadTypeFiles()
    {
        $types = array();
        foreach ($this->getFiles() as $file) {

            $parsedInput = Yaml::parse($file);

            if (is_array($parsedInput)) {
                $types = array_merge_recursive($types, $parsedInput);
            }
        }

        return $types;
    }


    /**
     * @return array
     */
    public function getTypes()
    {
        $types = array();
        $types = array_merge_recursive($types, $this->loadTypeFiles());

        return $types;
    }


    /**
     * @param string $type
     *
     * @return array|bool
     */
    public function getType($type)
    {
        if (strlen(($type)) === 0) {
            return false;
        }

        $types = $this->getTypes();
        if (!isset($types[$type])) {
            return false;
        }

        return $types[$type];
    }


    /**
     * @param string $type
     *
     * @return array|bool
     */
    public function getTypeProperties($type)
    {
        $type = $this->getType($type);
        if (!$type || !isset($type['properties']) || !is_array($type['properties'])) {
            return false;
        }

        $properties = array();

        foreach ($type['properties'] as $group => $typeProperties) {

            $properties = array_merge_recursive($properties, $typeProperties);
        }

        return $properties;
    }


    /**
     * @param string $type
     *
     * @return array|bool
     */
    public function getTypePropertyGroups($type)
    {
        $type = $this->getType($type);
        if (!$type || !isset($type['properties']) || !is_array($type['properties'])) {
            return false;
        }

        return $type['properties'];
    }
}
