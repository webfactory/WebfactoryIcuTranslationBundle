# ICU Translation Bundle #

This bundle enables [ICU message formatting](http://userguide.icu-project.org/formatparse) in Symfony 2 translations.

ICU message formatting can be used to solve problems like pluralization, gender specific translations
or locale specific date formatting.

## Installation ##

Install the bundle via composer:

    php composer.phar require webfactory/icu-translation-bundle *

Enable the bundle in your kernel:

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new \Webfactory\TranslationBundle\WebfactoryTranslatorBundle()
        );
    }

Message formatting is now supported for all translations.

## Usage ##

Formatting is optional: If no formatting is needed, then simple messages can be used.

Formatted messages use a special syntax that defines how to translate the message
depending on provided translation parameters.

The following examples show translation messages for several common use cases.

### Gender Specific Translations ###

Conditions can be used to realize gender specific translations.
The following message expects the parameter "gender_of_participant", which is
either "female" or "male":

    {gender_of_participant, select,
        female {She participated in the course.}
        other {He participated in the course.}
    }

Depending on the parameter value, the correct translation will be chosen.

### Pluralization ###

This translation message expects an integer value as "number_of_participants" parameter:

    {number_of_participants, plural,
        =0 {Nobody is participating.}
        =1 {One person participates.}
        other {# persons are participating.}
    }

Depending on the actual number the correct translation is selected. The *#* in
the last translation will be substituted by the value of "number_of_participants".

Please refer to <Resources/doc/index.rst> for more details about the formatting options.