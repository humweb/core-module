<?php

namespace Humweb\Core\Support;

use InvalidArgumentException;

/**
 * Assert.
 */
class Asserts
{
    public static function float($value, $message = '')
    {
        if ( ! is_float($value)) {
            throw new InvalidArgumentException(sprintf($message ?: 'Expected a float. Actual: %s', self::getValueType($value)));
        }
    }


    protected static function getValueType($value)
    {
        return is_object($value) ? get_class($value) : gettype($value);
    }


    public static function boolean($value, $message = '')
    {
        if ( ! is_bool($value)) {
            throw new InvalidArgumentException(sprintf($message ?: 'Expected a boolean. Actual: %s', self::getValueType($value)));
        }
    }


    public static function string($value, $message = '')
    {
        if ( ! is_string($value)) {
            throw new InvalidArgumentException(sprintf($message ?: 'Expected a string. Actual: %s', self::getValueType($value)));
        }
    }


    public static function integer($value, $message = '')
    {
        if ( ! is_int($value)) {
            throw new InvalidArgumentException(sprintf($message ?: 'Expected a integer. Actual: %s', self::getValueType($value)));
        }
    }


    public static function isEmpty($value, $message = '')
    {
        if ( ! empty($value)) {
            throw new InvalidArgumentException(sprintf($message ?: 'Expected an empty value. Actual: %s', self::getValueType($value)));
        }
    }


    public static function notEmpty($value, $message = '')
    {
        if (empty($value)) {
            throw new InvalidArgumentException(sprintf($message ?: 'Expected a non-empty value. Actual: %s', self::getValueType($value)));
        }
    }


    public static function null($value, $message = '')
    {
        if (null !== $value) {
            throw new InvalidArgumentException(sprintf($message ?: 'Expected null. Actual: %s', self::getValueType($value)));
        }
    }


    public static function notNull($value, $message = '')
    {
        if (null === $value) {
            throw new InvalidArgumentException($message ?: 'Expected a value other than null.');
        }
    }


    public static function isArray($value, $message = '')
    {
        if ( ! is_array($value)) {
            throw new InvalidArgumentException(sprintf($message ?: 'Expected a array. Actual: %s', self::getValueType($value)));
        }
    }


    public static function classExists($value, $message = '')
    {
        if ( ! class_exists($value)) {
            throw new InvalidArgumentException(sprintf($message ?: 'Expected a class. Actual: %s', self::getValueType($value)));
        }
    }


    public static function isInstanceOf($value, $class, $message = '')
    {
        if ( ! ($value instanceof $class)) {
            throw new InvalidArgumentException(sprintf($message ?: 'Expected an instance of %2$s. Actual: %s', self::getValueType($value), $class));
        }
    }
}
