<?php
/**
 *  This file is part of the Aplorm package.
 *
 *  (c) Nicolas Moral <n.moral@live.fr>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Aplorm\Lexer\Interpreter;

use Aplorm\Lexer\Exception\ClassNotFoundException;
use Aplorm\Lexer\Exception\ClassPartNotFoundException;
use Aplorm\Lexer\Exception\ConstantNotFoundException;
use Aplorm\Lexer\Exception\InvalidAnnotationConfigurationException;
use Aplorm\Lexer\LexedPartInterface;
use Aplorm\Lexer\TypeInterface;

class Interpreter
{
    protected static array $parts = [];

    protected static array $interpretedPart = [];

    protected static ?string $currentClassName = null;
    protected static ?string $fullyQualifiedClassName = null;
    protected static ?string $classNamespace = null;

    protected const STRING_CONST_VALUE = [
        'false' => false,
        'true' => true,
        'null' => null,
    ];

    public static function interprete(array &$parts)
    {
        self::$parts = &$parts;
        self::handleClass();
    }

    protected static function handleClass()
    {
        $part = self::getPart(LexedPartInterface::CLASS_NAME_PART);
        self::$currentClassName = $part['className'];
        self::$fullyQualifiedClassName = $part['fullyQualifiedClassName'];
        self::$classNamespace = $part['namespace'];

        if (isset($part['annotations']) && !empty($part['annotations'])) {
            self::handleAnnotations($part['annotations']);
        }

        self::$interpretedPart[LexedPartInterface::CLASS_NAME_PART] = &$part;
        unset($part);
        var_dump(self::$interpretedPart[LexedPartInterface::CLASS_NAME_PART]);
    }

    protected static function handleAnnotations(array &$annotations)
    {
        $interpretedAnnotations = [];
        foreach ($annotations as $key => &$annotation) {
            self::handleAnnotation($annotation);
            $key = \get_class($annotation);
            $interpretedAnnotations[$key] = $annotation;
        }

        $annotations = $interpretedAnnotations;
        unset($interpretedAnnotations);
    }

    protected static function handleAnnotation(array &$annotation)
    {
        $namedParameter = false;
        $anonymousParameter = false;
        $annotationParameter = [];
        foreach ($annotation['params'] as &$parameter) {
            self::handleParameter($parameter);
            if (isset($parameter['name'])) {
                $namedParameter = true;
                $annotationParameter[$parameter['name']] = $parameter['value'];
            } else {
                $anonymousParameter = true;
                $annotationParameter[] = $parameter['value'];
            }
        }

        if ($namedParameter && $anonymousParameter) {
            throw new InvalidAnnotationConfigurationException('You can\'t use named and anonymousParameter');
        }
        $className = self::findClass($annotation['name']);

        if ($namedParameter) {
            $annotation = new $className($annotationParameter);
        } elseif ($annotationParameter) {
            $annotation = new $className(...$annotationParameter);
        } else {
            $annotation = new $className();
        }
        unset($className, $annotationParameter);
    }

    protected static function handleParameter(array &$parameter)
    {
        switch ($parameter['type']) {
            case TypeInterface::NUMBER_CONSTANT_TYPE:
                $parameter['value'] = $parameter['value'] + 0;

                break;
            case TypeInterface::CLASS_CONSTANT_TYPE:
                self::handleConstant($parameter);

                break;
            case TypeInterface::ARRAY_TYPE:
            case TypeInterface::OBJECT_TYPE:
                foreach ($parameter['value'] as &$value) {
                    self::handleParameter($value);
                    $value = $value['value'];
                }

                break;
            case TypeInterface::ANNOTATION_TYPE:
                self::handleAnnotation($parameter['value']);

                break;
            case TypeInterface::OTHER_CONSTANT_TYPE:
                self::handleGlobalConstant($parameter['value']);

                break;
            case TypeInterface::STRING_TYPE:
            default:
                // code...
                break;
        }
    }

    protected static function handleGlobalConstant(string &$value)
    {
        if (isset(self::STRING_CONST_VALUE[strtolower($value)])) {
            $value = self::STRING_CONST_VALUE[strtolower($value)];

            return;
        }

        $value = self::getConstantValue($value);
    }

    protected static function handleConstant(array &$parameter)
    {
        [
            $alias,
            $constant
        ] = explode('::', $parameter['value']);

        if ('self' === $alias || 'static' === $alias || $alias === self::$currentClassName) {
            $parameter['value'] = self::getClassConstantValue(self::$fullyQualifiedClassName, $constant);

            return;
        }

        $fullyQualifiedName = self::findClass($alias);

        $parameter['value'] = self::getClassConstantValue($fullyQualifiedName, $constant);
    }

    protected static function findClass(string $alias): string
    {
        $aliases = self::getPart(LexedPartInterface::CLASS_ALIASES_PART);

        if (isset($aliases[$alias])) {
            $fullyQualifiedName = $aliases[$alias];
        } elseif (class_exists($alias)) {
            $fullyQualifiedName = $alias;
        } elseif (class_exists(self::$classNamespace.'\\'.$alias)) {
            $fullyQualifiedName = self::$classNamespace.'\\'.$alias;
        } elseif (class_exists('\\'.$alias)) {
            $fullyQualifiedName = '\\'.$alias;
        } else {
            throw new ClassNotFoundException($alias, self::$fullyQualifiedClassName);
        }

        return $fullyQualifiedName;
    }

    protected static function getClassConstantValue(string $fullyQualifiedName, string $constant)
    {
        return self::getConstantValue($fullyQualifiedName.'::'.$constant);
    }

    protected static function getConstantValue(string $constant)
    {
        if (!\defined($constant)) {
            throw new ConstantNotFoundException($constant);
        }

        return \constant($constant);
    }

    protected static function &getPart(string $partName)
    {
        if (!isset(self::$parts[$partName])) {
            throw new ClassPartNotFoundException($partName);
        }

        return self::$parts[$partName];
    }
}
