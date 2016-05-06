<?php

namespace Grossum\CoreBundle\Entity\EntityTrait;

trait CreatedAtControlTrait
{
    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    
    /**
     * Executes automatically before inserting record
     */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();

        return $this;
    }
}
