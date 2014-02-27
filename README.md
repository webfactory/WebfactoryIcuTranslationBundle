# ICU Translator Bundle #

This bundle enables [ICU message formatting](http://userguide.icu-project.org/formatparse) in Symfony 2 translations.

ICU message formatting can be used to solve problems like pluralization, gender specific translations
or locale specific date formatting.

## Installation ##

Install the bundle via composer:

    php composer.phar require webfactory/icu-translator-bundle *

Enable the bundle in your kernel:

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new \Webfactory\TranslatorBundle\WebfactoryTranslatorBundle()
        );
    }