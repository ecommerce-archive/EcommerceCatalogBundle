<?php

namespace Ecommerce\Bundle\CatalogBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Philipp Wahala <philipp.wahala@gmail.com>
 */
class EcommerceCatalogExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('controllers.xml');

        $useJmsSerializer = true === $config['use_jms_serializer']
            || ('auto' === $config['use_jms_serializer']
                && isset($bundles['JMSSerializerBundle'])
            )
        ;

        if ($config['persistence']['phpcr']['enabled']) {
            $this->loadPhpcr($config['persistence']['phpcr'], $loader, $container, $useJmsSerializer);
        }
    }

    public function loadPhpcr($config, XmlFileLoader $loader, ContainerBuilder $container, $useJmsSerializer)
    {
        $container->setParameter($this->getAlias().'.backend_type_phpcr', true);
        $prefix = $this->getAlias().'.persistence.phpcr';

        $keys = array(
            'manager_name' => 'manager_name',
            'product_basepath' => 'product_basepath',
            'product_class' => 'product.class',
        );

        foreach ($keys as $sourceKey => $targetKey) {
            if (isset($config[$sourceKey])) {
                $container->setParameter(
                    $prefix.'.'.$targetKey,
                    $config[$sourceKey]
                );
            }
        }

        // load phpcr specific configuration
        $loader->load('persistence-phpcr.xml');

        if ($useJmsSerializer) {
            // load phpcr specific serializer configuration
            $loader->load('serializer-phpcr.xml');
        }
    }
}
