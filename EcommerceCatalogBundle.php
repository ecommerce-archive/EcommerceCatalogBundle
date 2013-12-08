<?php

namespace Ecommerce\Bundle\CatalogBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Doctrine\Bundle\PHPCRBundle\DependencyInjection\Compiler\DoctrinePhpcrMappingsPass;

/**
 * @author Philipp Wahala <philipp.wahala@gmail.com>
 */
class EcommerceCatalogBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        if (class_exists('Doctrine\Bundle\PHPCRBundle\DependencyInjection\Compiler\DoctrinePhpcrMappingsPass')) {
            $container->addCompilerPass(
                DoctrinePhpcrMappingsPass::createXmlMappingDriver(
                    array(
                        realpath(__DIR__.'/Resources/config/doctrine-phpcr') => 'Ecommerce\Bundle\CatalogBundle\Doctrine\Phpcr',
                    ),
                    array('ecommerce_catalog.persistence.phpcr.manager_name'),
                    'ecommerce_catalog.backend_type_phpcr'
                )
            );
        }
    }
}
