<?php

namespace Grossum\CoreBundle\Entity\EntityTrait;

/**
 * @deprecated since 0.1.1 and will be removed in 0.2. Use DatesAwareTrait instead
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
