<?php

namespace Grossum\CoreBundle\Entity\EntityTrait;

trait SaveUpdateInManagerTrait
{
    /**
     * @param $entity
     */
    public function save($entity)
    {
        $this->objectManager->persist($entity);
        $this->update($entity);
    }


    /**
     * @param $entity
     */
    public function update($entity)
    {
        $this->objectManager->flush($entity);
    }
}
