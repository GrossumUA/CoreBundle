Grossum Core Bundle
===========

[![Total Downloads](https://poser.pugx.org/grossum/core-bundle/downloads.svg)](https://packagist.org/packages/grossum/core-bundle) [![Latest Stable Version](https://poser.pugx.org/grossum/core-bundle/v/stable.svg)](https://packagist.org/packages/grossum/core-bundle) [![Latest Unstable Version](https://poser.pugx.org/grossum/core-bundle/v/unstable.svg)](https://packagist.org/packages/grossum/core-bundle)


Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
composer require grossum/core-bundle "0.1.*@dev"
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.git 

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding the following line in the `app/AppKernel.php`
file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Grossum\CoreBundle\GrossumCoreBundle(),
        );

        // ...
    }

    // ...
}
```
