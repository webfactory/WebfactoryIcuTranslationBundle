<?php

namespace Webfactory\TranslatorBundle\Tests\Translator;

use Webfactory\TranslatorBundle\Translator\FormatterDecorator;
use Webfactory\TranslatorBundle\Translator\Formatting\IntlFormatter;
use Webfactory\TranslatorBundle\Translator\Formatting\MessageLexer;
use Webfactory\TranslatorBundle\Translator\Formatting\MessageParser;
use Webfactory\TranslatorBundle\Translator\Formatting\TwigParameterNormalizer;

/**
 * Tests the formatter decorator for translators.
 */
class FormatterDecoratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * System under test.
     *
     * @var \Webfactory\TranslatorBundle\Translator\FormatterDecorator
     */
    protected $decorator = null;

    /**
     * The simulated inner translator.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $translator = null;

    protected function setUp()
    {
        parent::setUp();
        $this->translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $this->decorator  = new FormatterDecorator(
            $this->translator,
            new TwigParameterNormalizer(new IntlFormatter(new MessageParser(new MessageLexer())))
        );
    }

    protected function tearDown()
    {
        $this->decorator  = null;
        $this->translator = null;
        parent::tearDown();
    }

    /**
     * Checks if the decorator implements the Translator interface.
     */
    public function testImplementsTranslatorInterface()
    {
        $this->assertInstanceOf('Symfony\Component\Translation\TranslatorInterface', $this->decorator);
    }

    /**
     * Checks if the decorator forwards calls to trans() to the inner translator.
     */
    public function testDecoratorForwardsTransCalls()
    {
        $this->translator->expects($this->once())
                         ->method('trans')
                         ->will($this->returnValue('test'));

        $this->decorator->trans('test');
    }

    /**
     * Checks if the decorator forwards calls to transChoice() to the inner translator.
     */
    public function testDecoratorForwardsTransChoiceCalls()
    {
        $this->translator->expects($this->once())
                         ->method('transChoice')
                         ->will($this->returnValue('test'));

        $this->decorator->transChoice('test', 42);
    }

    /**
     * Checks if the decorator forwards calls to setLocale() to the inner translator.
     */
    public function testDecoratorForwardsSetLocaleCalls()
    {
        $this->translator->expects($this->once())
                         ->method('setLocale')
                         ->with('de_DE');

        $this->decorator->setLocale('de_DE');
    }

    /**
     * Checks if getLocale() returns the locale value from the inner translator.
     */
    public function testGetLocaleReturnsLocaleFromInnerTranslator()
    {
        $this->translator->expects($this->once())
                         ->method('getLocale')
                         ->will($this->returnValue('fr'));

        $this->assertEquals('fr', $this->decorator->getLocale());
    }

    /**
     * Ensures that the decorator does not corrupt messages that do not contain further
     * formatting instructions.
     */
    public function testDecoratorDoesNotModifyTranslationWithoutFormattingInstructions()
    {
        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue('This is a test message.'));

        $this->assertEquals('This is a test message.', $this->decorator->trans('test'));
    }

    /**
     * Checks if the decorator replaces variables in ICU syntax.
     */
    public function testDecoratorReplacesNumberedVariablesInFormatterFormat()
    {
        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue('We need {0,number,integer} tests.'));

        $this->assertEquals('We need 42 tests.', $this->decorator->trans('test', array(42)));
    }

    public function testDecoratorReplacesNamedVariables()
    {
        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue('We need {numberOfTests, number,integer} tests.'));

        $this->assertEquals('We need 42 tests.', $this->decorator->trans('test', array('numberOfTests' => 42)));
    }

    /**
     * Checks if the formatter resolves select expressions correctly.
     */
    public function testDecoratorResolvesSelectExpressionCorrectly()
    {
        $message = '{gender, select,'            . PHP_EOL
                 . '    female {She is tested.}' . PHP_EOL
                 . '    male {He is tested.}'    . PHP_EOL
                 . '    other {unknown}'         . PHP_EOL
                 . '}';
        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue($message));

        $this->assertEquals('She is tested.', $this->decorator->trans('test', array('gender' => 'female')));
    }

    /**
     * Ensures that the decorator formats messages that are returned by transChoice().
     */
    public function testDecoratorFormatsMessagesReturnedByTransChoice()
    {
        $this->translator->expects($this->any())
                         ->method('transChoice')
                         ->will($this->returnValue('We need {0,number,integer} tests.'));

        $this->assertEquals('We need 42 tests.', $this->decorator->transChoice('test', 7, array(42)));
    }

    /**
     * Ensures that the decorator resolves an expression as expected when the checked variable
     * is not passed.
     *
     * In such a case, the case should be resolved to other.
     */
    public function testDecoratorResolvesSelectExpressionCorrectlyIfCheckedVariableIsNotSet()
    {
        $message = '{available, select,'        . PHP_EOL
                 . '    yes {It is available!}' . PHP_EOL
                 . '    other {Not available.}' . PHP_EOL
                 . '}';
        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue($message));

        $this->assertEquals('Not available.', $this->decorator->trans('test', array()));
    }

    /**
     * Ensures that the decorator does not fail if a used parameter is not passed.
     */
    public function testDecoratorCanHandleUndefinedParameters()
    {
        $message = 'Hello {name}!';
        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue($message));

        $this->assertEquals('Hello !', $this->decorator->trans('test', array()));
    }

    /**
     * Ensures that the decorator can handle empty messages without raising errors.
     */
    public function testDecoratorCanHandleEmptyMessages()
    {
        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue(''));

        $this->setExpectedException(null);
        $this->decorator->trans('test');
    }

    /**
     * Ensures that the decorator can handle messages that contain only whitespace
     * without raising errors.
     */
    public function testDecoratorCanHandleMessagesThatContainOnlyBlanks()
    {
        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue('   '));

        $this->setExpectedException(null);
        $this->decorator->trans('test');
    }

    /**
     * Ensures that the decorator normalizes formatter exceptions.
     */
    public function testDecoratorNormalizesFormatterException()
    {
        $formatter = $this->getMock('Webfactory\TranslatorBundle\Translator\Formatting\FormatterInterface');
        $formatter->expects($this->once())
                  ->method('format')
                  ->will($this->throwException(new \RuntimeException('Formatter exception.')));
        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue('any'));

        $decorator = new FormatterDecorator($this->translator, $formatter);

        $this->setExpectedException('Webfactory\TranslatorBundle\Translator\FormattingException');
        $decorator->trans('test', array('test' => 'value'), 'messages', 'en');
    }

    /**
     * Checks if the translator uses the parameters that follow the Twig
     * naming convention: %name%
     */
    public function testDecoratorUsesParametersInTwigFormat()
    {
        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue('We need {numberOfTests, number,integer} tests.'));

        // The parameter is referenced as usual, but it is provided in Twig format.
        $this->assertEquals('We need 42 tests.', $this->decorator->trans('test', array('%numberOfTests%' => 42)));
    }

    /**
     * Ensures that HTML can be used in simple messages.
     */
    public function testDecoratorSupportsSimpleMessagesWithHtml()
    {
        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue('Hello <strong>{name}</strong>!'));

        $translation = $this->decorator->trans('test', array('name' => 'webfactory'));
        $this->assertEquals('Hello <strong>webfactory</strong>!', $translation);
    }

    /**
     * Checks if HTML can be used in the different parts of a select expression.
     */
    public function testDecoratorSupportsSelectExpressionsWithHtml()
    {
        $message = '{location, select,'                                    . PHP_EOL
                 . '    webfactory {<strong>Best</strong> place to work.}' . PHP_EOL
                 . '    other {Unknown location.}'                         . PHP_EOL
                 . '}';
        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue($message));

        $translation = $this->decorator->trans('test', array('location' => 'webfactory'));
        $this->assertEquals('<strong>Best</strong> place to work.', $translation);
    }

    /**
     * Checks if multi line text is supported in select expressions.
     */
    public function testDecoratorSupportsMultiLineTextInSelectExpression()
    {
        $message = '{location, select,'            . PHP_EOL
                 . '    webfactory {'              . PHP_EOL
                 . 'This is'                       . PHP_EOL
                 . 'a multi line'                  . PHP_EOL
                 . 'text.'                         . PHP_EOL
                 . '    }'                         . PHP_EOL
                 . '    other {Unknown location.}' . PHP_EOL
                 . '}';
        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue($message));

        $translation = $this->decorator->trans('test', array('location' => 'webfactory'));
        $expected = 'This is' . PHP_EOL
                  . 'a multi line' . PHP_EOL
                  . 'text.';
        $this->assertEquals($expected, trim($translation));
    }

    /**
     * Checks if the decorator can handle umlauts in messages.
     */
    public function testDecoratorCanHandleUmlautsInMessages()
    {
        $message = 'Schlüsselkompetenzen sind bei {name} vorhanden..';
        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue($message));

        $this->setExpectedException(null);
        $this->decorator->trans('test', array('name' => 'Eddy'));
    }

    /**
     * Checks if the decorator supports slashes in messages.
     */
    public function testDecoratorCanHandleSlashesInMessages()
    {
        $message = 'He/she is called {name}.';
        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue($message));

        $this->setExpectedException(null);
        $this->decorator->trans('test', array('name' => 'Eddy'));
    }

    /**
     * Checks if the decorator resolves nested select expressions correctly.
     */
    public function testDecoratorCanHandleNestedSelectExpressions()
    {
        $message = '{a, select,'          . PHP_EOL
                 . '    yes {{b, select,' . PHP_EOL
                 . '        yes {ab}'     . PHP_EOL
                 . '        other {a}'    . PHP_EOL
                 . '    }}'               . PHP_EOL
                 . '    other {none}'     . PHP_EOL
                 . '}';

        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue($message));

        $this->setExpectedException(null);
        $this->assertEquals('ab', $this->decorator->trans('test', array('a' => 'yes', 'b' => 'yes')));
    }

    /**
     * Checks if plural formatting is supported.
     */
    public function testDecoratorSupportsPluralFormatting()
    {
        $message = '{number_of_participants, plural,'         . PHP_EOL
                 . '    =0 {Nobody is participating.}'        . PHP_EOL
                 . '    =1 {One person participates.}'        . PHP_EOL
                 . '    other {# persons are participating.}' . PHP_EOL
                 . '}';

        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue($message));

        $translation = $this->decorator->trans('test', array('number_of_participants' => 0));
        $this->assertEquals('Nobody is participating.', $translation);
    }

    /**
     * Checks if the decorator replaces a hash (#) in a plural statement
     * by the checked number.
     */
    public function testDecoratorReplacesHashInPluralFormatCorrectly()
    {
        $message = '{number_of_participants, plural,'         . PHP_EOL
                 . '    =0 {Nobody is participating.}'        . PHP_EOL
                 . '    =1 {One person participates.}'        . PHP_EOL
                 . '    other {# persons are participating.}' . PHP_EOL
                 . '}';

        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue($message));

        $translation = $this->decorator->trans('test', array('number_of_participants' => 42));
        $this->assertEquals('42 persons are participating.', $translation);
    }

    /**
     * Checks if a checked variable can be referenced inside a plural condition.
     */
    public function testVariableFromPluralConditionCanBeReferencedInTranslation()
    {
        $message = '{number_of_participants, plural,'                                   . PHP_EOL
            . '    =0 {Nobody is participating.}'                                       . PHP_EOL
            . '    other {{number_of_participants, number} persons are participating.}' . PHP_EOL
            . '}';

        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue($message));

        $translation = $this->decorator->trans('test', array('number_of_participants' => 42));
        $this->assertEquals('42 persons are participating.', $translation);
    }

    /**
     * Checks if the plural categories (zero, one, few, ...) work as expected.
     */
    public function testDecoratorSupportsPluralCategories()
    {
        $message = '{number_of_participants, plural,'                                        . PHP_EOL
                 . '    one {One person participates.}'                                      . PHP_EOL
                 . '    other {{number_of_participants, number} persons are participating.}' . PHP_EOL
                 . '}';

        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue($message));

        $translation = $this->decorator->trans('test', array('number_of_participants' => 1), null, 'en');
        $this->assertEquals('One person participates.', $translation);
    }

    /**
     * Checks if the number formatting depends on the locale.
     */
    public function testDecoratorFormatsNumbersDependingOnLocale()
    {
        $message = '{number_of_persons, number} have been counted.';

        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue($message));

        $translationEn = $this->decorator->trans('test', array('number_of_persons' => 1024), null, 'en');
        $this->assertEquals('1,024 have been counted.', $translationEn);
        $translationDe = $this->decorator->trans('test', array('number_of_persons' => 1024), null, 'de');
        $this->assertEquals('1.024 have been counted.', $translationDe);
    }

    /**
     * Checks if the translator formats currencies correctly.
     */
    public function testDecoratorFormatsCurrencyDependingOnLocale()
    {
        $message = 'Available for just {price, number, currency}.';

        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue($message));

        $translationEnGb = $this->decorator->trans('test', array('price' => 99.99), null, 'en_GB');
        $expected = 'Available for just £99.99.';
        $this->assertEquals($expected, $translationEnGb);
        $translationDe = $this->decorator->trans('test', array('price' => 99.99), null, 'de_DE');
        // Notice: ICU seems to use a special whitespace that is added in front of the currency.
        $expected = 'Available for just 99,99 €.';
        $this->assertEquals($expected, $translationDe);
    }

    /**
     * Checks if dates are formatted correctly.
     *
     * Please note: The formatter requires dates as timestamp, otherwise
     *              1970-01-01 is used, regardless of the content of the
     *              date object.
     */
    public function testDecoratorSupportsDateFormatting()
    {
        $message = 'Born on {birthDate, date, short}.';

        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue($message));

        $date = new \DateTime('1986-02-04');
        $translationEnGb = $this->decorator->trans('test', array('birthDate' => $date->getTimestamp()), null, 'en_GB');
        $expected = 'Born on 04/02/1986.';
        $this->assertEquals($expected, $translationEnGb);
        $translationDe = $this->decorator->trans('test', array('birthDate' => $date->getTimestamp()), null, 'de_DE');
        $expected = 'Born on 04.02.86.';
        $this->assertEquals($expected, $translationDe);
    }

    /**
     * Checks if the decorator allows double quotes in text fragments.
     */
    public function testDecoratorSupportsDoubleQuotesInText()
    {
        $message = 'It is called "Formatting" by {name}.';

        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue($message));

        $translation = $this->decorator->trans('test', array('name' => 'Theo Translator'));
        $expected    = 'It is called "Formatting" by Theo Translator.';
        $this->assertEquals($expected, $translation);
    }

    /**
     * Checks if the decorator allows single quotes in text fragments.
     *
     * Notice: Is this the correct behavior? Should not the text be treated
     *         as quoted while the quotes are removed?
     */
    public function testDecoratorSupportsSingleQuotesInText()
    {
        $message = "It is called 'Formatting' by {name}.";

        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue($message));

        $translation = $this->decorator->trans('test', array('name' => 'Theo Translator'));
        $expected    = "It is called 'Formatting' by Theo Translator.";
        $this->assertEquals($expected, $translation);
    }

    /**
     * Ensures that escaped braces are not touched by the translator.
     */
    public function testDecoratorDoesNotChangeEscapedBraces()
    {
        $message = "The placeholder '{'name'}' is escaped.";

        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue($message));

        $translation = $this->decorator->trans('test');
        $expected    = 'The placeholder {name} is escaped.';
        $this->assertEquals($expected, $translation);
    }

    /**
     * Checks if escaped single quotes are handled correctly.
     */
    public function testDecoratorSupportsEscapedSingleQuotes()
    {
        $message = "The character '' is called single quote by {name}.";

        $this->translator->expects($this->any())
                         ->method('trans')
                         ->will($this->returnValue($message));

        $translation = $this->decorator->trans('test', array('name' => 'Translator'));
        $expected    = "The character ' is called single quote by Translator.";
        $this->assertEquals($expected, $translation);
    }

}
