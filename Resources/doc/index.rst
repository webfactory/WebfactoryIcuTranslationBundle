========================
New Translation Features
========================


What's new?
-----------

Messages become more powerful and provide features like translations
depending on certain conditions.

These new features are fully optional: There is still no difference when using
simple translations. If needed, advanced features may be used.

The following sections will provide a brief introduction into the new
translation message features.


Conditions
----------

If needed, conditions can be used to provide translations for different circumstances
(for example depending on the gender).

The following example shows a conditional message:

    {gender_of_participant, select,
        female {She participated in the course.}
        other {He participated in the course.}
    }

If the variable "gender_of_participant" contains the value "female", then the sentence
"She participated in the course." will be shown. Otherwise "He participated in the course."
is used as translation.

Please note, that each conditional statement needs an "other" section. If that section is
missing, then an error will occur when the translation is used on the website.


Nested Conditions
~~~~~~~~~~~~~~~~~

Even more complex scenarios are possible as conditions can be nested if required:

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

To improve readability, it might be useful to move the most complex conditions
to the outside.


Long Translations
~~~~~~~~~~~~~~~~~

If necessary, long translations in conditions can be split into several lines:

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
by the curly braces:

    {gender_of_participant, select,
        female {She
    participated
    in
    the
    course.}
        other {He participated in the course.}
    }


Further Features
----------------

Variable Replacement and Formatting
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Variable names are placed within curly braces and are replaced by concrete values during translation:

    Hello {name}!


Variables can also have a type, which is noted after the variable name, separated by a comma:

    In this course, {number_of_participants, number} are participating.

In this case, the type "number" is applied.  Depending on the locale, the correct thousands and decimal
separator will be chosen automatically.
Therefore, with locale "en" the number is shown as "1,024", whereas in german ("de") "1.024"
will be used as representation.


By marking number as currencies, the currency symbol will be automatically added at the correct position:

    Available for just {price, number, currency}.

Formatting in en_GB: "Available for just £99.99."
Formatting in de_DE: "Available for just 99,99 €."


For variables that are considered a date, local formats are available:

    Born on {birthDate, date, short}.

Formatting in en_GB: "Born on 04/02/1986."
Formatting in de_DE: "Born on 04.02.86."


Plural Formatting
~~~~~~~~~~~~~~~~~

Various plural rules can be applied via "plural" condition:

    {number_of_participants, plural,
        =0 {Nobody is participating.}
        =1 {One person participates.}
        other {# persons are participating.}
    }

In this case the correct translation is chosen depending on the number_of_participants.
In the "other" case the hash ("#") is replaced by the number of participants.

It is also possible to reference the number via variable name, but in that case the type
"number" must be provided to avoid a type error:

    {number_of_participants, plural,
        =0 {Nobody is participating.}
        =1 {One person participates.}
        other {{number_of_participants, number} persons are participating.}
    }

Additionally, there are several plural categories for each language, which can be used
to distinguish between the different cases:

    {number_of_participants, plural,
        one {One person participates.}
        other {{number_of_participants, number} persons are participating.}
    }

Which categories exist in a language can be looked up at [http://www.unicode.org/cldr/charts/latest/supplemental/language_plural_rules.html].
In English, there are just the categories "one" and "other".

Languages with more complex plural rules provide several categories. For example Arabic defines
"zero", "one", "two", "few", "many" and "other" as category.


Special Characters and Escaping
-------------------------------

Any character except curly braces and single quotes can be used within translations.

If a curly brace is needed it should be escaped with single quotes:

    This '{'token'}' is escaped.

The above message will be transformed into "This {token} is escaped.".

If a single quote is needed it must be preceded by another single quote:

   The character '' is called single quote.

This message is transformed into "The character ' is called single quote.".