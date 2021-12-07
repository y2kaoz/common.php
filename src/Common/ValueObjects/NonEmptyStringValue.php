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

namespace Y2KaoZ\Common\ValueObjects;

/**
 * A non-empty string value object
 *
 */
class NonEmptyStringValue extends StringValue
{
    /**
     * @param string $value The source value.
     */
    public function __construct(string $value)
    {
        $value = trim($value);
        if (empty($value)) {
            throw new \Exception("Invalid input string, the string is empty.");
        }
        parent::__construct($value);
    }
}
