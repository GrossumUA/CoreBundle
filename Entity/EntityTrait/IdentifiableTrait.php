<?php

namespace Grossum\CoreBundle\Entity\EntityTrait;

trait IdentifiableTrait
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @return int Id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id Id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
