<?php

namespace Grossum\CoreBundle\Entity\EntityTrait;

trait UpdateAtControlTrait
{
    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

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
