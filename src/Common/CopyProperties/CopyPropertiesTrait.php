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

use ReflectionProperty;

/**
 * This is the default implementation for Y2KaoZ\Common\CopyProperties\CopyPropertiesInterface
 *
 * This interface+trait is more helpfull on classes that have public member properties to copy
 * or exposes private member properties using __set.
 *
 * @example class CopyTarget implements \Y2KaoZ\Common\CopyProperties\CopyPropertiesInterface {
 *              use \Y2KaoZ\Common\CopyProperties\CopyPropertiesTrait;
 *          }
 */
trait CopyPropertiesTrait
{
    use CopyPropertiesBaseTrait;

    /** @param array<string,ReflectionProperty> $namedProperties */
    private function setValue(array $namedProperties, string $property, mixed $value): void
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
            $namedProperties[$property]->setValue($this, $value);
        } else {
            if (method_exists($this, "__set")) {
                try {
                    $this->__set($property, $value);
                } catch (\Throwable $t) {
                    $this->{$property} = $value;
                }
            } else {
                $this->{$property} = $value;
            }
        }
    }

    /**
     * Copies the matching properties from source object to the class
     * that implements this interface using CopyPropertiesTrait
     *
     * @param object $source The source to copy from
     * @return static The modified object.
     */
    public function fromObject(object $source): static
    {
        $namedProperties = static::getNamedProperties(static::class);
        foreach (array_keys($namedProperties) as $property) {
            if (property_exists($source, $property)) {
                $this->setValue($namedProperties, $property, $source->{$property});
            }
        }
        return $this;
    }

    /**
     * Copies the matching keys's values from source the array to the class
     * that implements this interface using CopyPropertiesTrait
     *
     * @param array<string,mixed> $source The source to copy from
     * @return static The modified object.
     */
    public function fromArray(array $source): static
    {
        if (!empty($source)) {
            $namedProperties = static::getNamedProperties(static::class);
            foreach (array_keys($namedProperties) as $property) {
                if (array_key_exists($property, $source)) {
                    $this->setValue($namedProperties, $property, $source[$property]);
                }
            }
        }
        return $this;
    }

    /**
     * Copies the matching named parameters to the class
     * that implements this interface using CopyPropertiesTrait
     *
     * @param mixed ...$source The source to copy from
     * @return static The modified object.
     */
    public function fromParams(mixed ...$source): static
    {
        foreach ($source as $key => $_value) {
            if (!is_string($key)) {
                throw new \Exception("Invalid parameter key '$key'.");
            }
        }
        /** @var array<string,mixed> $strArray */
        $strArray = $source;
        $this->fromArray($strArray);
        return $this;
    }
}
