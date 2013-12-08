<?php

namespace Ecommerce\Bundle\CatalogBundle\Product\Properties;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

class ProductPropertiesRegistry implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    private $propertyFiles;

    /**
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function addPropertyFiles(array $files = array())
    {
        $this->propertyFiles = $files;
    }

    public function getPropertyFiles()
    {
        return (array)$this->propertyFiles;
    }


    /**
     * @return array
     */
    public function loadPropertyFiles()
    {
        $properties = array();
        foreach ($this->getPropertyFiles() as $propertyFile) {

            $parsedInput = Yaml::parse($propertyFile);

            if (is_array($parsedInput)) {
                $properties = array_merge_recursive($properties, $parsedInput);
            }
        }

        return $properties;
    }


    /**
     * @return array
     */
    public function getProperties()
    {
        $properties = array();
        $properties = array_merge_recursive($properties, $this->loadPropertyFiles());

        return $properties;
    }
}
