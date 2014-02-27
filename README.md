# ICU Translation Bundle #

[![Build Status](https://travis-ci.org/webfactory/icu-translations-bundle.png?branch=master)](https://travis-ci.org/webfactory/icu-translations-bundle)
[![Coverage Status](https://coveralls.io/repos/webfactory/icu-translations-bundle/badge.png?branch=master)](https://coveralls.io/r/webfactory/icu-translations-bundle?branch=master)

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

### Placeholder Substitution ###

Message placeholders are enclosed by curly braces and will be substituted during formatting:

    Hello {name}!

Things get more interesting when parameter type are involved, which require
a special treatment depending on the locale.

The following message uses correct thousands separators depending on the locale:

    In this course, {number_of_participants, number} are participating.

This means, that the value 1024 would be shown as "1,024" for the locale "en", whereas
in german ("de") "1.024" is used as representation.

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

Please refer to [advanced documentation](Resources/doc/index.rst) for more details about the formatting options.