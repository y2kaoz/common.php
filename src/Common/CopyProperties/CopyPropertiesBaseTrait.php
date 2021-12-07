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
use Y2KaoZ\Common\PropertiesCache\PropertiesCache;

trait CopyPropertiesBaseTrait
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
}
