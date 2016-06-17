<?php

namespace Grossum\CoreBundle\Entity\EntityTrait;

/**
 * @deprecated since version 2.0
 */
trait DateTimeControlTrait
{
    /**
     * Executes automatically before inserting record
     */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();

        return $this;
    }

    /**
     * Executes automatically before inserting/updating record
     */
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new \DateTime();

        return $this;
    }
}
