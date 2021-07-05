<?php

/*
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License only.
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
 */

declare(strict_types=1);

namespace Y2KaoZ\Common;

use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use Y2KaoZ\Common\Interfaces\CopyPropertiesInterface;
use Y2KaoZ\Common\PropertiesCache;

class CopyProperties
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

    public static function fromObject(object &$target, object $source): void
    {
        $namedProperties = static::getNamedProperties($target::class);
        foreach (array_keys($namedProperties) as $property) {
            if (property_exists($source, $property)) {
                static::setValue($namedProperties, $property, $source->{$property}, $target);
            }
        }
    }

    /** @param array<string,mixed> $source*/
    public static function fromArray(object &$target, array $source): void
    {
        if (!empty($source)) {
            $namedProperties = static::getNamedProperties($target::class);
            foreach (array_keys($namedProperties) as $property) {
                if (array_key_exists($property, $source)) {
                    static::setValue($namedProperties, $property, $source[$property], $target);
                }
            }
        }
    }

    public static function fromParams(object &$target, mixed ...$source): void
    {
        foreach ($source as $key => $_value) {
            assert(is_string($key));
        }
        /** @var array<string,mixed> $strArray */
        $strArray = $source;
        static::fromArray($target, $strArray);
    }
}
