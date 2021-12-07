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

namespace Y2KaoZ\Common\PropertiesCache;

use ReflectionClass;
use ReflectionProperty;

/**
 * Provides a cache to store class reflextion properties.
 *
 * This class is used in reflection to store class properties for reuse.
 * 
 * 
 */
final class PropertiesCache
{
    /** @var array<string,array<int, ReflectionProperty[]>> */
    private static array $properties = [];

    /** @var array<string,array<int, string[]>> */
    private static array $propertyNames = [];

    /**
     * Loads and stores in a cache the filtered properties for the given class
     * @param class-string $class The class to retrieve properties from.
     * @return ReflectionProperty[] The properties for class
     */
    public static function getProperties(string $class, int $filter = ReflectionProperty::IS_PUBLIC): array
    {
        if (!isset(static::$properties[$class][$filter])) {
            if (!class_exists($class, false)) {
                throw new \Exception("class '$class' is not defined.");
            }
            static::$properties[$class][$filter] = (new ReflectionClass($class))->getProperties($filter);
        }
        return static::$properties[$class][$filter];
    }

    /**
     * Loads and stores in a cache the names of the filtered properties for the given class
     * @param class-string $class The class to retrieve properties names from.
     * @return string[] The name of the properties for class
     */
    public static function getPropertyNames(string $class, int $filter = ReflectionProperty::IS_PUBLIC): array
    {
        if (!isset(static::$propertyNames[$class][$filter])) {
            static::$propertyNames[$class][$filter] = array_map(
                fn(ReflectionProperty $property): string=>$property->getName(),
                static::getProperties($class, $filter)
            );
        }
        return static::$propertyNames[$class][$filter];
    }
}
