# ICU Translation Bundle #

[![Build Status](https://travis-ci.org/webfactory/icu-translation-bundle.png?branch=master)](https://travis-ci.org/webfactory/icu-translation-bundle)
[![Coverage Status](https://coveralls.io/repos/webfactory/icu-translation-bundle/badge.png?branch=master)](https://coveralls.io/r/webfactory/icu-translation-bundle?branch=master)

While the [Symfony2 translation component](http://symfony.com/doc/current/components/translation/index.html) does a
great job in most cases, it can become difficult to use if you need conditions other than numbers (e.g. gender) or
nested conditions. This is where the ICU Translation Bundle steps in. Using the [International Components for Unicode
project](http://site.icu-project.org/)'s standard message format, it enhances the Symfony component with arbitrary and
nested conditions, as well as easy-to-use localized number and date formatting. The enhancement is non-invasive, i.e.
you don't have to touch your former messages, they'll still work as usual.

## Installation ##

Assuming you've already [enabled and configured the Symfony2 translation component](http://symfony.com/doc/current/book/translation.html#book-translation-configuration),
all you have to do is to install the bundle via [composer](https://getcomposer.org) with something like this:

    php composer.phar require webfactory/icu-translation-bundle

(We use [Semantic Versioning](http://semver.org/), so as soon as a version tagged 1.0.0 is available, you'll probably
want to use something like ~1.0 as the version string.)

As usual, enable the bundle in your kernel:

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new \Webfactory\IcuTranslationBundle\WebfactoryIcuTranslationBundle()
        );
        // ...
    }

## Usage ##

To use the bundle's enhancements, you need to use a special syntax with curly braces in your translation messages. The
following examples show this syntax for common use cases. For a comprehensive list, please refer to [bundle
documentation](Resources/doc/index.rst).

### Number formatting with a parameter type ###

In your messages, you can specify "number" as a parameter type after a variable. If so, the output is localized with the
correct thousands separator and decimal mark. See this example message stored under the key "message-number":

    1 mile = {mile_to_metres, number} metres

In a controller, the example could look like this:

    $translator = $this->get('translator');
    $output = $translator->trans(
        'message-number',
        array('%mile_to_metres%' => 1609.34)
    );

E.g. for the locale "en", the output will be "1 mile = 1,609.34 metres", while for the locale "de" it will be "1 mile =
1.609,34 metres" (or "1 Meile = 1.609,34 Meter" with a proper translation).

For other parameter types such as date, see the bundle documentation.

### Gender Specific Translations ###

Gender specific translations are a special case of arbitrary conditions. Conditions are denoted by the key word "select"
after the variable, followed by possible variable values and their respective messages. See the following example
message stored for the locale "en" under the key "message-gender":

    {gender, select,
        female {She spent all her money on horses}
        other {He spent all his money on horses}
    }

If your controller looks something like this:

    $output = $translator->trans(
        'message-gender',
        array('%gender%' => 'male')
    );

the output will be "He spent all his money on horses" for the locale "en".

Why didn't we list "female" and "male" as possible variable values in the message, but "female" and "other" instead?
Find out in the bundle documentation.

### More Readable Pluralization ###

While [Symfony's translation component already supports pluralization](http://symfony.com/doc/current/components/translation/usage.html#component-translation-pluralization),
we think the ICU Translation Bundle provides it in a more readable way. Analogously to conditions, pluralizations are
denoted by the key word "plural" after the variable, followed by possible variable values and their respective messages.
See the following example message stored for the locale "en" under the key "message-pluralization":

    {number_of_participants, plural,
        =0 {Nobody is participating}
        =1 {One person participates}
        other {# persons are participating}
    }

If your controller looks something like this:

    $output = $translator->trans(
        'message-pluralization',
        array('%number_of_participants%' => 2)
    );

The output for the locale "en" will be: "2 persons are participating".

Note that you can distinguish both between exact numbers like with "=0" and [Unicode Common Locale Data Repository
number categories](http://www.unicode.org/cldr/charts/latest/supplemental/language_plural_rules.html) like "other". Also
note that the number sign "#" becomes substituted with the value of the variable, 2 in this example.

Now that you've got an idea of the ICU translation bundle's features, we once more invite you to read the [bundle
documentation](Resources/doc/index.rst).

## Changelog ##

### 0.2.2 -> 0.2.3 ###

The ``GracefulExceptionsDecorator`` logs all types of exception now, not just instances of ``FormattingException``.

Credits, Copyright and License
------------------------------
Copyright 2012-2015 webfactory GmbH, Bonn. Code released under [the MIT license](LICENSE).

- <http://www.webfactory.de>
- <http://twitter.com/webfactory>
