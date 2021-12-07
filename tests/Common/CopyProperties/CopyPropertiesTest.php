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

namespace tests\Common\CopyProperties\CopyPropertiesTest;

use stdClass;
use PHPUnit\Framework\TestCase;
use Y2KaoZ\Common\CopyProperties\CopyProperties;

class Target
{
    public int $id = 0;
    public ?string $name = null;

    protected ?string $protVal = null;
    private ?string $privVal = null;

    public function __set(string $name, mixed $value): void
    {
        if ($name === "protVal") {
            $this->protVal = $value;
            return;
        }
        throw new \Exception("property $name is not valid for '" . static::class . "'");
    }

    public function __get(string $name): mixed
    {
        if ($name === "protVal") {
            return $this->protVal;
        }
        if ($name === "privVal") {
            return $this->privVal;
        }
        throw new \Exception("property $name is not valid for '" . static::class . "'");
    }
}

class NonTypedTarget
{
    public $id = 0;
    public $name = null;
    protected $protVal = null;
    private $privVal = null;

    public function __set(string $name, mixed $value): void
    {
        if ($name === "protVal") {
            $this->protVal = $value;
            return;
        }
        throw new \Exception("property $name is not valid for '" . static::class . "'");
    }

    public function __get(string $name): mixed
    {
        if ($name === "protVal") {
            return $this->protVal;
        }
        if ($name === "privVal") {
            return $this->privVal;
        }
        throw new \Exception("property $name is not valid for '" . static::class . "'");
    }
}

class CopyPropertiesTest extends TestCase
{
    public function testCopyPropertiesFromObject(): void
    {
        $target = new Target();
        $source = new stdClass();
        $source->id = 1;
        $source->name = "one";
        $source->protVal = "protVal";
        $source->privVal = "privVal";
        CopyProperties::fromObject($target, $source);
        $this->assertEquals($target->id, $source->id);
        $this->assertEquals($target->name, $source->name);
        $this->assertEquals($target->protVal, $source->protVal);
        $this->assertEquals($target->privVal, null);
    }

    public function testCopyPropertiesFromArray(): void
    {
        $target = new Target();
        $source = [];
        $source["id"] = 1;
        $source["name"] = "one";
        $source["protVal"] = "protVal";
        $source["privVal"] = "privVal";
        CopyProperties::fromArray($target, $source);
        $this->assertEquals($target->id, $source["id"]);
        $this->assertEquals($target->name, $source["name"]);
        $this->assertEquals($target->protVal, $source["protVal"]);
        $this->assertEquals($target->privVal, null);
    }

    public function testCopyPropertiesFromParams(): void
    {
        $target = new Target();
        CopyProperties::fromParams(
            $target,
            id:1,
            name:"one",
            protVal: "protVal",
            privVal: "privVal"
        );
        $this->assertEquals($target->id, 1);
        $this->assertEquals($target->name, "one");
        $this->assertEquals($target->protVal, "protVal");
        $this->assertEquals($target->privVal, null);
    }

    public function testCopyPropertiesFromNoNamedParamsException(): void
    {
        $this->expectException(\Exception::class);
        $target = new Target();
        CopyProperties::fromParams(
            $target,
            1,
            "one",
            "protVal",
            "privVal"
        );
    }

    public function testCopyPropertiesAllowsNull(): void
    {
        $target = new Target();
        CopyProperties::fromParams(
            $target,
            name:null
        );
        $this->assertEquals($target->id, 0);
        $this->assertEquals($target->name, null);
        $this->assertEquals($target->protVal, null);
        $this->assertEquals($target->privVal, null);
    }


    public function testCopyPropertiesNotAllowsNullException(): void
    {
        $this->expectException(\Exception::class);
        $target = new Target();
        CopyProperties::fromParams(
            $target,
            id:null
        );
    }

    public function testCopyNotTyped(): void
    {
        $target = new NonTypedTarget();
        $target->id = 1;
        $target->name = "one";
        $target->protVal = "protVal";
        CopyProperties::fromParams(
            $target,
            id:2,
            name:"two",
            protVal: true,
            privVal: false,
        );
        $this->assertEquals($target->id, 2);
        $this->assertEquals($target->name, "two");
        $this->assertEquals($target->protVal, true);
        $this->assertEquals($target->privVal, null);
    }
}
