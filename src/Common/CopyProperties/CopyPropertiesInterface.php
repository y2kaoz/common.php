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

/**
 * This Interface is for types that use the trait Y2KaoZ\Common\CopyProperties\CopyPropertiesTrait.
 *
 * This is mostly usefull for type definition in function signatures and classes.
 * This interface+trait is more helpfull on classes that have private member properties to copy.
 *
 * 
 * 
 * @example class CopyTarget implements \Y2KaoZ\Common\CopyProperties\CopyPropertiesInterface {
 *              use \Y2KaoZ\Common\CopyProperties\CopyPropertiesTrait;
 *          }
 */
interface CopyPropertiesInterface
{
    /**
     * Copies the matching properties from source object to the class
     * that implements this interface using CopyPropertiesTrait
     *
     * @param object $source The source to copy from
     * @return static The modified object.
     */
    public function fromObject(object $source): static;
    /**
     * Copies the matching keys's values from source the array to the class
     * that implements this interface using CopyPropertiesTrait
     *
     * @param array<string,mixed> $source The source to copy from
     * @return static The modified object.
     */
    public function fromArray(array $source): static;
    /**
     * Copies the matching named parameters to the class
     * that implements this interface using CopyPropertiesTrait
     *
     * @param mixed ...$source The source to copy from
     * @return static The modified object.
     */
    public function fromParams(mixed ...$source): static;
}
