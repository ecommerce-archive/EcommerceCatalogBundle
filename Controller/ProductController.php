<?php

namespace Ecommerce\Bundle\CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProductController extends Controller
{
    public function viewAction($id)
    {
        return $this->render(
            'EcommerceCatalogBundle:Product:view.html.twig',
            array(
                'id' => $id,
            )
        );
    }
}
