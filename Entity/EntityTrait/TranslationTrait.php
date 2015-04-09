<?php

namespace Grossum\CoreBundle\Entity\EntityTrait;

trait TranslationTrait
{
    /**
     * @param string $variable
     * @param string $locale
     * @return mixed
     */
    public function getTranslation($variable, $locale)
    {
        $nameVariableTranslated = $variable . $locale;
        return $this->$nameVariableTranslated;
    }
}
