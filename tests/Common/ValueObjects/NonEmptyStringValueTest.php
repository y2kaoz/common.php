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

namespace tests\Common\PropertiesCache\NonEmptyStringValueTest;

use PHPUnit\Framework\TestCase;
use Y2KaoZ\Common\ValueObjects\NonEmptyStringValue;

class NonEmptyStringValueTest extends TestCase
{
    public function testEmptyString()
    {
        $this->expectException(\Exception::class);
        $string = new NonEmptyStringValue("");
        $this->assertEmpty($string->getValue());
        $this->assertEmpty(strval($string));
        $this->assertEquals(strval($string), $string->getValue());
    }

    public function testWithValue()
    {
        $value = "hola";
        $string = new NonEmptyStringValue($value);
        $this->assertNotEmpty($string->getValue());
        $this->assertEquals($string->getValue(), $value);
        $this->assertNotEmpty(strval($string));
        $this->assertEquals(strval($string), $value);
        $this->assertEquals(strval($string), $string->getValue());
    }
}
