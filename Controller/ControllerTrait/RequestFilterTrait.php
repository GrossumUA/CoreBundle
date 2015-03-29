<?php

namespace Grossum\CoreBundle\Controller\ControllerTrait;

trait RequestFilterTrait
{
    public function trimRequestValue($param)
    {
        return trim($param);
    }
}
