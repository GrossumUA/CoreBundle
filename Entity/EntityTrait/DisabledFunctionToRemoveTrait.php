<?php

namespace Grossum\CoreBundle\Entity\EntityTrait;

use Sonata\AdminBundle\Route\RouteCollection;

trait DisabledFunctionToRemoveTrait
{

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
    }
}
