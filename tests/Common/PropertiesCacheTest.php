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

namespace Y2KaoZ\Common\PropertiesCacheTest;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Y2KaoZ\Common\PropertiesCache;

class EmptyExistingClass
{

}

class ExistingClass
{
    public $public1 = null;
    public $public2 = null;
    private $private1 = null;
    private $private2 = null;
    protected $protected1 = null;
    protected $protected2 = null;
}

class PropertiesCacheTest extends TestCase
{
    public function testGetPropertiesWithNonExistingClassThrows(): void
    {
        $this->expectException(\Exception::class);
        $properties = PropertiesCache::getProperties("NonExistingClass");
    }
    public function testGetPropertyNamesWithNonExistingClassThrows(): void
    {
        $this->expectException(\Exception::class);
        $properties = PropertiesCache::getPropertyNames("NonExistingClass");
    }

    public function testPublicOnEmptyExistingClass(): void
    {
        $properties = PropertiesCache::getProperties(EmptyExistingClass::class, ReflectionProperty::IS_PUBLIC);
        $propertynames = PropertiesCache::getPropertyNames(EmptyExistingClass::class, ReflectionProperty::IS_PUBLIC);
        $this->assertEmpty($properties);
        $this->assertEmpty($propertynames);
    }
    public function testPrivateOnEmptyExistingClass(): void
    {
        $properties = PropertiesCache::getProperties(EmptyExistingClass::class, ReflectionProperty::IS_PRIVATE);
        $propertynames = PropertiesCache::getPropertyNames(EmptyExistingClass::class, ReflectionProperty::IS_PRIVATE);
        $this->assertEmpty($properties);
        $this->assertEmpty($propertynames);
    }
    public function testProtectedOnEmptyExistingClass(): void
    {
        $properties = PropertiesCache::getProperties(EmptyExistingClass::class, ReflectionProperty::IS_PROTECTED);
        $propertynames = PropertiesCache::getPropertyNames(EmptyExistingClass::class, ReflectionProperty::IS_PROTECTED);
        $this->assertEmpty($properties);
        $this->assertEmpty($propertynames);
    }

    public function testPublicOnExistingClass(): void
    {
        $properties = PropertiesCache::getProperties(ExistingClass::class, ReflectionProperty::IS_PUBLIC);
        $propertynames = PropertiesCache::getPropertyNames(ExistingClass::class, ReflectionProperty::IS_PUBLIC);
        $this->assertEquals(count($properties), 2);
        foreach ($properties as $property) {
            $this->assertTrue($property instanceof ReflectionProperty);
        }
        $this->assertEquals($propertynames, ["public1", "public2"]);
    }
    public function testPrivateOnExistingClass(): void
    {
        $properties = PropertiesCache::getProperties(ExistingClass::class, ReflectionProperty::IS_PRIVATE);
        $propertynames = PropertiesCache::getPropertyNames(ExistingClass::class, ReflectionProperty::IS_PRIVATE);
        $this->assertEquals(count($properties), 2);
        foreach ($properties as $property) {
            $this->assertTrue($property instanceof ReflectionProperty);
        }
        $this->assertEquals($propertynames, ["private1", "private2"]);
    }
    public function testProtectedOnExistingClass(): void
    {
        $properties = PropertiesCache::getProperties(ExistingClass::class, ReflectionProperty::IS_PROTECTED);
        $propertynames = PropertiesCache::getPropertyNames(ExistingClass::class, ReflectionProperty::IS_PROTECTED);
        $this->assertEquals(count($properties), 2);
        foreach ($properties as $property) {
            $this->assertTrue($property instanceof ReflectionProperty);
        }
        $this->assertEquals($propertynames, ["protected1", "protected2"]);
    }
    public function testAllOnExistingClass(): void
    {
        $filter = ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED;
        $properties = PropertiesCache::getProperties(ExistingClass::class, $filter);
        $propertynames = PropertiesCache::getPropertyNames(ExistingClass::class, $filter);
        $this->assertEquals(count($properties), 6);
        foreach ($properties as $property) {
            $this->assertTrue($property instanceof ReflectionProperty);
        }
        $this->assertEquals($propertynames, ["public1", "public2", "private1", "private2", "protected1", "protected2"]);
    }
}
