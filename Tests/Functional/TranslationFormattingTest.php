<?php

namespace Webfactory\TranslationBundle\Tests\Functional;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webfactory\IcuTranslationBundle\DependencyInjection\DecorateTranslatorCompilerPass;
use Webfactory\IcuTranslationBundle\DependencyInjection\WebfactoryIcuTranslationExtension;

/**
 * Tests the translation formatting features of the translator that is provided by this bundle.
 */
class TranslationFormattingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * System under test.
     *
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator = null;

    /**
     * Initializes the test environment.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->translator = $this->createTranslator();
    }

    /**
     * Cleans up the test environment.
     */
    protected function tearDown()
    {
        $this->translator = null;
        parent::tearDown();
    }

    /**
     * Ensures that the translator does not corrupt messages that do not contain further
     * formatting instructions.
     */
    public function testTranslatorDoesNotModifyTranslationWithoutFormattingInstructions()
    {
        $message = 'This is a test message.';
        $this->assertEquals($message, $this->translator->trans($message));
    }

    /**
     * Checks if the translator replaces variables in ICU syntax.
     */
    public function testTranslatorReplacesIndexedVariablesInFormatterFormat()
    {
        $message = 'We need {0,number,integer} tests.';
        $this->assertEquals('We need 42 tests.', $this->translator->trans($message, ['%0%' => 42]));
    }

    /**
     * Checks if the translator replaces named variables.
     */
    public function testTranslatorReplacesNamedVariables()
    {
        $message = 'We need {numberOfTests, number,integer} tests.';
        $this->assertEquals('We need 42 tests.', $this->translator->trans($message, ['%numberOfTests%' => 42]));
    }

    /**
     * Checks if the formatter resolves select expressions correctly.
     */
    public function testTranslatorResolvesSelectExpressionCorrectly()
    {
        $message = '{gender, select,'.PHP_EOL
                 .'    female {She is tested.}'.PHP_EOL
                 .'    male {He is tested.}'.PHP_EOL
                 .'    other {unknown}'.PHP_EOL
                 .'}';

        $this->assertEquals('She is tested.', $this->translator->trans($message, ['%gender%' => 'female']));
    }

    /**
     * Ensures that the translator formats messages that are returned by transChoice().
     */
    public function testTranslatorFormatsMessagesReturnedByTransChoice()
    {
        $message = 'We need {0,number,integer} tests.';
        $this->assertEquals('We need 42 tests.', $this->translator->transChoice($message, 7, ['%0%' => 42]));
    }

    /**
     * Ensures that the translator resolves an expression as expected when the checked variable
     * is not passed.
     *
     * In such a case, the case should be resolved to other.
     */
    public function testTranslatorResolvesSelectExpressionCorrectlyIfCheckedVariableIsNotSet()
    {
        $message = '{available, select,'.PHP_EOL
                 .'    yes {It is available!}'.PHP_EOL
                 .'    other {Not available.}'.PHP_EOL
                 .'}';

        $this->assertEquals('Not available.', $this->translator->trans($message, []));
    }

    /**
     * Ensures that the translator does not fail if a used parameter is not passed.
     */
    public function testTranslatorCanHandleUndefinedParameters()
    {
        $message = 'Hello {name}!';

        $this->assertEquals('Hello !', $this->translator->trans($message, []));
    }

    /**
     * Ensures that the translator can handle empty messages without raising errors.
     */
    public function testTranslatorCanHandleEmptyMessages()
    {
        $this->setExpectedException(null);
        $this->translator->trans('');
    }

    /**
     * Ensures that the translator can handle messages that contain only whitespace
     * without raising errors.
     */
    public function testTranslatorCanHandleMessagesThatContainOnlyBlanks()
    {
        $this->setExpectedException(null);
        $this->translator->trans('   ');
    }

    /**
     * Ensures that HTML can be used in simple messages.
     */
    public function testTranslatorSupportsSimpleMessagesWithHtml()
    {
        $message = 'Hello <strong>{name}</strong>!';
        $translation = $this->translator->trans($message, ['%name%' => 'webfactory']);
        $this->assertEquals('Hello <strong>webfactory</strong>!', $translation);
    }

    /**
     * Checks if HTML can be used in the different parts of a select expression.
     */
    public function testTranslatorSupportsSelectExpressionsWithHtml()
    {
        $message = '{location, select,'.PHP_EOL
                 .'    webfactory {<strong>Best</strong> place to work.}'.PHP_EOL
                 .'    other {Unknown location.}'.PHP_EOL
                 .'}';

        $translation = $this->translator->trans($message, ['%location%' => 'webfactory']);
        $this->assertEquals('<strong>Best</strong> place to work.', $translation);
    }

    /**
     * Checks if multi line text is supported in select expressions.
     */
    public function testTranslatorSupportsMultiLineTextInSelectExpression()
    {
        $message = '{location, select,'.PHP_EOL
                 .'    webfactory {'.PHP_EOL
                 .'This is'.PHP_EOL
                 .'a multi line'.PHP_EOL
                 .'text.'.PHP_EOL
                 .'    }'.PHP_EOL
                 .'    other {Unknown location.}'.PHP_EOL
                 .'}';

        $translation = $this->translator->trans($message, ['%location%' => 'webfactory']);
        $expected = 'This is'.PHP_EOL
                  .'a multi line'.PHP_EOL
                  .'text.';
        $this->assertEquals($expected, trim($translation));
    }

    /**
     * Checks if the translator can handle umlauts in messages.
     */
    public function testTranslatorCanHandleUmlautsInMessages()
    {
        $message = 'Schlüsselkompetenzen sind bei {name} vorhanden.';

        $this->setExpectedException(null);
        $this->translator->trans($message, ['%name%' => 'Eddy']);
    }

    /**
     * Checks if the translator supports slashes in messages.
     */
    public function testTranslatorCanHandleSlashesInMessages()
    {
        $message = 'He/she is called {name}.';

        $this->setExpectedException(null);
        $this->translator->trans($message, ['%name%' => 'Eddy']);
    }

    /**
     * Checks if the translator resolves nested select expressions correctly.
     */
    public function testTranslatorCanHandleNestedSelectExpressions()
    {
        $message = '{a, select,'.PHP_EOL
                 .'    yes {{b, select,'.PHP_EOL
                 .'        yes {ab}'.PHP_EOL
                 .'        other {a}'.PHP_EOL
                 .'    }}'.PHP_EOL
                 .'    other {none}'.PHP_EOL
                 .'}';

        $this->setExpectedException(null);
        $this->assertEquals('ab', $this->translator->trans($message, ['%a%' => 'yes', '%b%' => 'yes']));
    }

    /**
     * Checks if plural formatting is supported.
     */
    public function testTranslatorSupportsPluralFormatting()
    {
        $message = '{number_of_participants, plural,'.PHP_EOL
                 .'    =0 {Nobody is participating.}'.PHP_EOL
                 .'    =1 {One person participates.}'.PHP_EOL
                 .'    other {# persons are participating.}'.PHP_EOL
                 .'}';

        $translation = $this->translator->trans($message, ['%number_of_participants%' => 0]);
        $this->assertEquals('Nobody is participating.', $translation);
    }

    /**
     * Checks if the translator replaces a hash (#) in a plural statement
     * by the checked number.
     */
    public function testTranslatorReplacesHashInPluralFormatCorrectly()
    {
        $message = '{number_of_participants, plural,'.PHP_EOL
                 .'    =0 {Nobody is participating.}'.PHP_EOL
                 .'    =1 {One person participates.}'.PHP_EOL
                 .'    other {# persons are participating.}'.PHP_EOL
                 .'}';

        $translation = $this->translator->trans($message, ['%number_of_participants%' => 42]);
        $this->assertEquals('42 persons are participating.', $translation);
    }

    /**
     * Checks if a checked variable can be referenced inside a plural condition.
     */
    public function testVariableFromPluralConditionCanBeReferencedInTranslation()
    {
        $message = '{number_of_participants, plural,'.PHP_EOL
                 .'    =0 {Nobody is participating.}'.PHP_EOL
                 .'    other {{number_of_participants, number} persons are participating.}'.PHP_EOL
                 .'}';

        $translation = $this->translator->trans($message, ['%number_of_participants%' => 42]);
        $this->assertEquals('42 persons are participating.', $translation);
    }

    /**
     * Checks if the plural categories (zero, one, few, ...) work as expected.
     */
    public function testTranslatorSupportsPluralCategories()
    {
        $message = '{number_of_participants, plural,'.PHP_EOL
                 .'    one {One person participates.}'.PHP_EOL
                 .'    other {{number_of_participants, number} persons are participating.}'.PHP_EOL
                 .'}';

        $translation = $this->translator->trans($message, ['%number_of_participants%' => 1], null, 'en');
        $this->assertEquals('One person participates.', $translation);
    }

    /**
     * Checks if the number formatting depends on the locale.
     */
    public function testTranslatorFormatsNumbersDependingOnLocale()
    {
        $message = '{number_of_persons, number} have been counted.';

        $translationEn = $this->translator->trans($message, ['%number_of_persons%' => 1024], null, 'en');
        $this->assertEquals('1,024 have been counted.', $translationEn);
        $translationDe = $this->translator->trans($message, ['%number_of_persons%' => 1024], null, 'de');
        $this->assertEquals('1.024 have been counted.', $translationDe);
    }

    /**
     * Checks if the translator formats currencies correctly.
     */
    public function testTranslatorFormatsCurrencyDependingOnLocale()
    {
        $message = 'Available for just {price, number, currency}.';

        $translationEnGb = $this->translator->trans($message, ['%price%' => 99.99], null, 'en_GB');
        $expected = 'Available for just £99.99.';
        $this->assertEquals($expected, $translationEnGb);
        $translationDe = $this->translator->trans($message, ['%price%' => 99.99], null, 'de_DE');
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
    public function testTranslatorSupportsDateFormatting()
    {
        $message = 'Born on {birthDate, date, short}.';

        $date = new \DateTime('1986-02-04');
        $translationEnGb = $this->translator->trans($message, ['%birthDate%' => $date->getTimestamp()], null, 'en_GB');
        $expected = 'Born on 04/02/1986.';
        $this->assertEquals($expected, $translationEnGb);
        $translationDe = $this->translator->trans($message, ['%birthDate%' => $date->getTimestamp()], null, 'de_DE');
        $expected = 'Born on 04.02.86.';
        $this->assertEquals($expected, $translationDe);
    }

    /**
     * Checks if the translator allows double quotes in text fragments.
     */
    public function testTranslatorSupportsDoubleQuotesInText()
    {
        $message = 'It is called "Formatting" by {name}.';

        $translation = $this->translator->trans($message, ['%name%' => 'Theo Translator']);
        $expected = 'It is called "Formatting" by Theo Translator.';
        $this->assertEquals($expected, $translation);
    }

    /**
     * Checks if the translator allows single quotes in text fragments.
     *
     * Notice: Is this the correct behavior? Should not the text be treated
     *         as quoted while the quotes are removed?
     */
    public function testTranslatorSupportsSingleQuotesInText()
    {
        $message = "It is called 'Formatting' by {name}.";

        $translation = $this->translator->trans($message, ['%name%' => 'Theo Translator']);
        $expected = "It is called 'Formatting' by Theo Translator.";
        $this->assertEquals($expected, $translation);
    }

    /**
     * Ensures that escaped braces are not touched by the translator.
     */
    public function testTranslatorDoesNotChangeEscapedBraces()
    {
        $message = "The placeholder '{'name'}' is escaped.";

        $translation = $this->translator->trans($message);
        $expected = 'The placeholder {name} is escaped.';
        $this->assertEquals($expected, $translation);
    }

    /**
     * Checks if escaped single quotes are handled correctly.
     */
    public function testTranslatorSupportsEscapedSingleQuotes()
    {
        $message = "The character '' is called single quote by {name}.";

        $translation = $this->translator->trans($message, ['%name%' => 'Translator']);
        $expected = "The character ' is called single quote by Translator.";
        $this->assertEquals($expected, $translation);
    }

    /**
     * Creates the translator that is used for in the tests.
     *
     * @return \Symfony\Component\Translation\TranslatorInterface
     */
    protected function createTranslator()
    {
        $builder = new ContainerBuilder();
        $builder->register('translator', '\Symfony\Component\Translation\Translator')->addArgument('en');
        $extension = new WebfactoryIcuTranslationExtension();
        $extension->load([], $builder);
        $builder->addCompilerPass(new DecorateTranslatorCompilerPass());
        $builder->compile();
        $translator = $builder->get('translator');
        $this->assertInstanceOf('\Symfony\Component\Translation\TranslatorInterface', $translator);

        return $translator;
    }
}
