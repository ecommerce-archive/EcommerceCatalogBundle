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

        $loader->load('services.xml');

        $loader->load('controllers.xml');


        if ($config['property_files'] && is_array($config['property_files'])) {
            $productPropertiesRegistry = $container->getDefinition('ecommerce_catalog.product_properties_registry');
            $productPropertiesRegistry->addMethodCall('addPropertyFiles', array($config['property_files']));
        }


        if ($config['type_files'] && is_array($config['type_files'])) {
            $productTypesRegistry = $container->getDefinition('ecommerce_catalog.product_types_registry');
            $productTypesRegistry->addMethodCall('addFiles', array($config['type_files']));
        }


        if ($config['persistence']['phpcr']['enabled']) {
            $this->loadPhpcr($config['persistence']['phpcr'], $loader, $container);

            // orm depends on phpcr (and it doesn’t make sense to use the orm features without phpcr)
            if ($config['persistence']['orm']['enabled']) {
                $this->loadOrm($config['persistence']['orm'], $loader, $container);
            }
        }
    }

    public function loadPhpcr($config, XmlFileLoader $loader, ContainerBuilder $container)
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


//        $productManager = $container->getDefinition('ecommerce_catalog.product_manager');
//        $productManager->replaceArgument(0, new Reference('doctrine_phpcr'));
//        $productManager->addMethodCall('setManagerName', array('%ecommerce_catalog.persistence.phpcr.manager_name%'));
    }

    public function loadOrm($config, XmlFileLoader $loader, ContainerBuilder $container)
    {
        $container->setParameter($this->getAlias().'.backend_type_orm', true);
        $prefix = $this->getAlias().'.persistence.orm';

        $keys = array(
            'manager_name' => 'manager_name',
            'product_reference_class' => 'product_reference.class',
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
        $loader->load('persistence-orm.xml');


//        $productManager = $container->getDefinition('ecommerce_catalog.product_manager');
//        $productManager->replaceArgument(0, new Reference('doctrine_phpcr'));
//        $productManager->addMethodCall('setManagerName', array('%ecommerce_catalog.persistence.phpcr.manager_name%'));
    }
}
