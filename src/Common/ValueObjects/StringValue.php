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

use Stringable;

/**
 * A base string value object 
 * 
 */
class StringValue implements Stringable
{
    /** @readonly */
    private string $value;

    public function __construct(string $value = "")
    {
        $this->value = $value;
    }
    public function getValue(): string
    {
        return $this->value;
    }
    public function __toString(): string
    {
        return $this->value;
    }
}
