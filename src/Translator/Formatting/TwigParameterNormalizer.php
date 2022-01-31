<?php

namespace Webfactory\IcuTranslationBundle\Translator\Formatting;

/**
 * Removes leading and trailing percentage signs ('%') from parameter
 * names, which are usually added by Twig.
 */
class TwigParameterNormalizer extends AbstractFormatterDecorator
{
    /**
     * Transforms parameter names if necessary.
     *
     * @param array(string=>mixed) $parameters
     *
     * @return array(string=>mixed)
     */
    protected function preProcessParameters(array $parameters)
    {
        foreach ($parameters as $name => $value) {
            /* @var $name string|integer */
            /* @var $value mixed */
            if (\is_int($name)) {
                // Do *not* convert integer keys to string.
                continue;
            }
            $newName = trim($name, '%');
            if ($name === $newName) {
                // Name did not use Twig delimiters.
                continue;
            }
            if (!isset($parameters[$newName])) {
                // Parameter does not conflict with an existing one, therefore,
                // pass it under the new name.
                $parameters[$newName] = $value;
            }
            unset($parameters[$name]);
        }

        return $parameters;
    }
}
