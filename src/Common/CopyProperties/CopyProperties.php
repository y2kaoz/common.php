<?php

/*
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License only.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 *
 * Written by Carlos Gonzalez<y2kaoz@gmail.com>
 */

declare(strict_types=1);

namespace Y2KaoZ\Common\CopyProperties;

use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use Y2KaoZ\Common\CopyProperties\CopyPropertiesInterface;
use Y2KaoZ\Common\PropertiesCache\PropertiesCache;

/**
 * This is an external class to copy properties from diferent sources into an object.
 * 
 */
final class CopyProperties
{
    /**
     * @param class-string $class
     * @return array<string,ReflectionProperty> */
    private static function getNamedProperties(string $class): array
    {
        $filter = ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE;
        return array_combine(
            PropertiesCache::getPropertyNames($class, $filter),
            PropertiesCache::getProperties($class, $filter)
        );
    }

    /** @param array<string,ReflectionProperty> $namedProperties */
    private static function getFirstTypeName(array $namedProperties, string $property): ?string
    {
        $type = $namedProperties[$property]->getType();
        if ($type instanceof ReflectionNamedType) {
            return $type->getName();
        } elseif ($type instanceof ReflectionUnionType) {
            $types = $type->getTypes();
            if (isset($types[0])) {
                return $types[0]->getName();
            }
        }
        return null;
    }

    /** @param array<string,ReflectionProperty> $namedProperties */
    private static function setValue(array $namedProperties, string $property, mixed $value, object &$target): void
    {
        if ($value === null) {
            $type = $namedProperties[$property]->getType();
            if ($type !== null && !$type->allowsNull()) {
                throw new \Exception("Property '$property' doesn't allow null.");
            }
        } else {
            $typeName = static::getFirstTypeName($namedProperties, $property);
            if ($typeName) {
                if ($typeName === "bool" && is_string($value)) {
                    $value = ($value === "true" || $value === "1") ? true : false;
                } elseif (settype($value, $typeName) === false) {
                    throw new \Exception("Unable to set value's type to '$typeName'");
                }
            }
        }
        if ($namedProperties[$property]->isPublic()) {
            $namedProperties[$property]->setValue($target, $value);
        } else {
            if (method_exists($target, "__set")) {
                try {
                    $target->__set($property, $value);
                } catch (\Throwable $t) {
                }
            }
        }
    }
    /**
     * Copies the matching properties from source object into the target class
     * @param object &$target The target to write to
     * @param object $source The source to copy from
     * @return object The modified object.
     */
    public static function fromObject(object &$target, object $source): object
    {
        $namedProperties = static::getNamedProperties($target::class);
        foreach (array_keys($namedProperties) as $property) {
            if (property_exists($source, $property)) {
                static::setValue($namedProperties, $property, $source->{$property}, $target);
            }
        }
        return $target;
    }

    /**
     * Copies the matching keys's values from source into the target class
     *
     * @param object &$target The target to write to
     * @param array<string,mixed> $source The source to copy from
     * @return object The modified object.
     */
    public static function fromArray(object &$target, array $source): object
    {
        if (!empty($source)) {
            $namedProperties = static::getNamedProperties($target::class);
            foreach (array_keys($namedProperties) as $property) {
                if (array_key_exists($property, $source)) {
                    static::setValue($namedProperties, $property, $source[$property], $target);
                }
            }
        }
        return $target;
    }
    /**
     * Copies the matching named parameters into the target class
     * @param object &$target The target to write to
     * @param mixed ...$source The source to copy from
     * @return object The modified object.
     */
    public static function fromParams(object &$target, mixed ...$source): object
    {
        foreach ($source as $key => $_value) {
            if (!is_string($key)) {
                throw new \Exception("Invalid parameter key '$key'.");
            }
        }
        /** @var array<string,mixed> $strArray */
        $strArray = $source;
        static::fromArray($target, $strArray);
        return $target;
    }
}
