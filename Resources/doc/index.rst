========================
New Translation Features
========================

Using the `International Components for Unicode project <http://site.icu-project.org/>`_'s standard message format, the
ICU Translation Bundle enhances the `Symfony translation component <http://symfony.com/doc/current/components/translation/index.html>`_
with arbitrary and nested conditions, as well as easy-to-use localized number and date formatting. The enhancement is
non-invasive, i.e. you don't have to touch your former messages, they'll still work as usual.

The following will introduce you to the new translation message features and format.


Variable Replacement and Formatting
-----------------------------------

Variable names are placed within curly braces and are replaced by concrete values during translation::

    Hello {name}!


Variables can also have a type, which is noted after the variable name, separated by a comma::

    In this course, {number_of_participants, number} are participating.

In this case, the type "number" is applied.  Depending on the locale, the correct thousands and decimal
separator will be chosen automatically.
Therefore, with locale "en" the number is shown as "1,024", whereas in german ("de") "1.024"
will be used as representation.


By marking number as currencies, the currency symbol will be automatically added at the correct position::

    Available for just {price, number, currency}.

Formatting in en_GB: "Available for just £99.99."
Formatting in de_DE: "Available for just 99,99 €."


For variables that are considered a date, local formats are available::

    Born on {birthDate, date, short}.

Formatting in en_GB: "Born on 04/02/1986."
Formatting in de_DE: "Born on 04.02.86."


Conditions
----------

You may use conditions to provide translations for different circumstances, e.g. the gender of a sentence's subject.
Conditions are denoted by the key word "select" after the variable, followed by possible variable values and their
respective messages. See the following example message stored for the locale "en" under the key "message-gender"::

    {gender, select,
        female {She spent all her money on horses}
        other {He spent all his money on horses}
    }
    
If your controller looks something like this::

    $output = $translator->trans(
        'message-gender',
        array('%gender%' => 'male')
    );
    
the output will be "He spent all his money on horses" for the locale "en".

**Note**: Each conditional statement needs an "other" section. If that section is missing, then an error will occur when
the translation is used on the website.


Nested Conditions
~~~~~~~~~~~~~~~~~

You may nest conditions for more complex scenarios::

    {course, select,
        translating_for_beginners {{gender_of_participant, select,
            female {She participated in the course Translating for Beginners.}
            other  {He participated in the course Translating for Beginners.}
        }}
        advanced_translation_methods {{gender_of_participant, select,
            female {She participated in the course Advanced Translation Methods.}
            other  {He participated in the course Advanced Translation Methods.}
        }}
        other {Unknown course.}
    }

For readability, you may want to write the conditions with the most cases more on the outside.


Long Translations
~~~~~~~~~~~~~~~~~

You may split long translations in conditions into several lines::

    {gender_of_participant, select,
        female {
    She
    participated
    in
    the
    course.
        }
        other {He participated in the course.}
    }

In this case, the sentence contains additional whitespace at the start and at the end, but this is
usually not a problem when used in a HTML context.

If a translation must not contain leading and trailing whitespace, then it has to be enclosed directly
by the curly braces::

    {gender_of_participant, select,
        female {She
    participated
    in
    the
    course.}
        other {He participated in the course.}
    }


Pluralization
-------------

While `Symfony's translation component <http://symfony.com/doc/current/components/translation/index.html>`_ already
supports pluralization, we think the ICU Translation Bundle provides it in a more readable way. Analogously to
conditions, pluralizations are denoted by the key word "plural" after the variable, followed by possible variable values
and their respective messages. See the following example message stored for the locale "en" under the key
"message-pluralization"::

    {number_of_participants, plural,
        =0 {Nobody is participating}
        =1 {One person participates}
        other {# persons are participating}
    }
    
If your controller looks something like this::

    $output = $translator->trans(
        'message-pluralization',
        array('%number_of_participants%' => 2)
    );
    
The output for the locale "en" will be: "2 persons are participating".

You may have noticed three issues:

1. To distinguish between exact numbers, you use the equals sign in front of the number.
2. The number sign "#" in a message becomes substituted with the value of the variable, 2 in this example.
3. You can distinguish both between exact numbers like with "=0" and something different like "other". Those are called
   number categories.
  
Number Categories
~~~~~~~~~~~~~~~~~

Some languages have more forms of number specific grammar and vocabulary. E.g. English has two forms: singular and
plural, while Bambara has only one form and Arabic has six. To abstract these forms for translations, the ICU Translation
Bundle supports the `Unicode Common Locale Data Repository number categories <http://www.unicode.org/cldr/charts/latest/supplemental/language_plural_rules.html>`_.

E.g. for English, these number categories are named "one" and "other". You use them as follows in your message::

    {number_of_participants, plural,
        one {One person participates.}
        other {{number_of_participants, number} persons are participating.}
    }


Escaping Special Characters
---------------------------

Any character can be used within translations. But curly braces and single quotes have to be escaped.

Escape curly braces by wrapping them in single quotes::

    This '{'token'}' is escaped

The output of this message will be "This {token} is escaped".

Escape single quotes by preceding them with another single quote::

   The character '' is called single quote

This message is transformed into "The character ' is called single quote".
