<?php

namespace Ecommerce\Bundle\CatalogBundle\Doctrine\Phpcr;

use Doctrine\ODM\PHPCR\Document\Generic;

use Jackalope\Node;

use Ecommerce\Bundle\CatalogBundle\Doctrine\Orm\ProductReferenceInterface;
use PHPCR\PropertyType;

class Product implements ProductInterface
{
    private $id;

    protected $nodename;

    /**
     * @var Generic
     */
    protected $parent;

    protected $status;

    /**
     * @var Node
     */
    public $node;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var ProductReferenceInterface
     */
    private $productReference;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;


    public function __construct($nodename, $parent)
    {
        $this->nodename = $nodename;
        $this->parent   = $parent;
        $this->status   = static::STATUS_CREATED;
    }


    public function getId()
    {
        return $this->id;
    }

    public function getIdentifier()
    {
        return $this->node->getIdentifier();
    }


    /**
     * @return string
     */
    public function getNodename()
    {
        return $this->nodename;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->nodename;
    }


    /**
     * @return string
     */
    public function isValidProduct()
    {
        return $this->hasProperty('name') && strlen($this->getProperty('name'));
    }

    /**
     * @param string $name the name of the document
     * @return Product
     */
    public function setNodename($name)
    {
        $this->nodename = $name;

        return $this;
    }


    public function getParent()
    {
        return $this->parent;
    }


    /**
     * @param mixed $parent
     * @return Product
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }



    public function getNode()
    {
        return $this->node;
    }

    /**
     * @param string $type
     *
     * @return Product
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }




    public function all()
    {
        return $this->node->getPropertiesValues();
    }


    /**
     * @param $name
     * @return array|mixed|\PHPCR\NodeInterface|resource
     */
    public function getProperty($name)
    {
        return $this->node->getPropertyValue($name);
    }


    /**
     * @param string $name
     * @param mixed  $default
     * @return array|mixed|null
     */
    public function get($name, $default = null)
    {
        return $this->node->getPropertyValueWithDefault($name, $default);
    }


    /**
     * @param $name
     * @return bool
     */
    public function hasProperty($name)
    {
        return $this->node->hasProperty($name);
    }


    /**
     * @param string $name
     * @param mixed  $value
     * @return Product
     */
    public function set($name, $value)
    {
        $this->node->setProperty($name, $value);

        return $this;
    }


    public function getTranslatedProperties()
    {
        $ignoredProperties = array(
            'jcr:primaryType',
            'jcr:mixinTypes',
            'phpcr:class',
            'phpcr:classparents',
            'jcr:uuid',
        );

        $properties = array_diff_key($this->node->getPropertiesValues(), array_flip($ignoredProperties));

        foreach ($properties as $key => $property) {
            if (is_array($property)
                && array_key_exists($key.'_locales', $properties)
                && is_array($propertyLocales = $properties[$key.'_locales'])
            ) {
                $properties[$key] = array_combine($propertyLocales, $property);
                unset($properties[$key.'_locales']);
            }
        }

        return $properties;
    }

    public function getTranslatedProperty($name)
    {
        if (!$this->node->hasProperty($name)) {
            return null;
        }
        $translations = $this->node->getPropertyValue($name);

        if (!is_array($translations) || !$this->node->hasProperty($name.'_locales')) {
            return null;
        }

        $locales = $this->node->getPropertyValue($name.'_locales');

        if (!is_array($locales) || count($translations) !== count($locales)) {
            return null;
        }


        return array_combine($locales, $translations);
    }


    public function setTranslatedProperty($name, $value)
    {
        if (!is_array($value)) {
            return null;
        }

        $translations = array_map(
            function($value) {
                return (string)$value;
            },
            array_values($value)
        );

        $this->node->setProperty($name, $translations, PropertyType::STRING);
        $this->node->setProperty($name.'_locales', array_keys($value), PropertyType::STRING);

        return true;
    }

    public function getIterator()
    {
        return $this->node->getProperties();
    }

    public function getPublicNodeProperties()
    {
        $properties = $this->node->getPropertiesValues();

        foreach ($properties as $key => $property) {
            if (strpos($key, 'jcr:') === 0
                || strpos($key, 'phpcr:') === 0
            ) {
                unset($properties[$key]);
            }
        }

        return $properties;
    }



    public function setProductReference($reference)
    {
        $this->productReference = $reference;

        return $this;
    }

    public function getProductReference()
    {
        return $this->productReference;
    }



    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt instanceof \DateTime ? $createdAt : new \DateTime();

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt instanceof \DateTime ? $updatedAt : new \DateTime();

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
