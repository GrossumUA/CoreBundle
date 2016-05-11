<?php

namespace Grossum\CoreBundle\Entity\EntityTrait;

trait EnabledTrait
{
    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
}
