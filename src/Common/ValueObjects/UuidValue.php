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
 * A uuid string value object
 *
 */
final class UuidValue extends NonEmptyStringValue
{
    //The UUID specification establishes 4 pre-defined namespaces:
    public const NS_DNS     = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
    public const NS_URL     = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';
    public const NS_OID     = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';
    public const NS_X500_DN = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

    /** @return non-empty-string */
    private static function getStringFromBytes(string $data): string
    {
        $result = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        assert(is_string($result) && !empty($result));
        return $result;
    }

    /** @param string|list<int|string> $value The source value as 2 64bit integers in an array or a valid uuid string. */
    public function __construct(string|array $value)
    {
        if (is_array($value)) {
            if (count($value) !== 2 || !is_numeric($value[0]) || !is_numeric($value[1])) {
                throw new \Exception("Invalid input array, there must be only 2 64bit integers.");
            }
            $hex = dechex(intval($value[0])) . dechex(intval($value[1]));
            if (empty($hex) || strlen($hex) !== 32) {
                throw new \Exception("Invalid input array format, there must be only 2 64bit integers.");
            }
            $value = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split($hex, 4));
            if (!is_string($value) || empty($value) || strlen($value) !== 36) {
                throw new \Exception("Invalid input array format, there must be only 2 64bit integers.");
            }
        }
        if (!preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-5][0-9a-f]{3}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $value)) {
            throw new \Exception("Invalid input string, the string is not a valid UUID.");
        }
        parent::__construct($value);
    }
    /**
     * Converts the uuid to a pair of integers for efficient storage.
     * @return list<int> The value as 2 */
    public function getIntPair(): array
    {
        $bytes = hex2bin(str_replace(array('-','{','}'), '', $this->getValue()));
        $hi = substr($bytes, 0, 16);
        $low = substr($bytes, 16, 16);
        return [ intval($hi, 16), intval($low, 16) ];
    }
    /** Generates a namespaced UUID using md5 as the hash algorithm */
    public static function generateVersion3(UuidValue $namespace, string $name): UuidValue
    {
        $nsBytes = hex2bin(str_replace(array('-','{','}'), '', $namespace->getValue()));
        $data = md5($nsBytes . $name, true);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x30); // Set version 3
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Set variant 1 (RFC 4122/DCE 1.1 UUID)
        $result = UuidValue::getStringFromBytes($data);
        return new UuidValue($result);
    }

    /** Generates a random UUID or one using the binary string $data */
    public static function generateVersion4(?string $data = null): UuidValue
    {
        $data = $data ?? random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // Set version 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Set variant 1 (RFC 4122/DCE 1.1 UUID)
        $result = UuidValue::getStringFromBytes($data);
        return new UuidValue($result);
    }

    /**
     * Timestamp-first are not mentioned in the UUID RFC; however,
     * they are a common variation of version-4 UUIDs. This format is sometimes called "Ordered UUIDs"
     */
    public static function generateTimestampFirst(): UuidValue
    {
        return UuidValue::generateVersion4(hex2bin(dechex(time())) . random_bytes(12));
    }

    /** Generates a namespaced UUID using sha1 as the hash algorithm */
    public static function generateVersion5(UuidValue $namespace, string $name): UuidValue
    {
        $nsBytes = hex2bin(str_replace(array('-','{','}'), '', $namespace->getValue()));
        $data = sha1($nsBytes . $name, true);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x50); // Set version 5
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Set variant 1 (RFC 4122/DCE 1.1 UUID)
        $result = UuidValue::getStringFromBytes(substr($data, 0, 16));
        return new UuidValue($result);
    }
}
