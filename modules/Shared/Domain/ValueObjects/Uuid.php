<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\ValueObjects;

use InvalidArgumentException;
use Stringable;

readonly class Uuid implements Stringable
{
    private function __construct(
        private string $value,
    ) {
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[4][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $this->value)) {
            throw new InvalidArgumentException("Invalid UUID: {$this->value}");
        }
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public static function generate(): static
    {
        return new static(self::uuid4());
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private static function uuid4(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr(ord($bytes[6]) & 0x0f | 0x40);
        $bytes[8] = chr(ord($bytes[8]) & 0x3f | 0x80);

        return sprintf(
            '%s-%s-%s-%s-%s',
            bin2hex(substr($bytes, 0, 4)),
            bin2hex(substr($bytes, 4, 2)),
            bin2hex(substr($bytes, 6, 2)),
            bin2hex(substr($bytes, 8, 2)),
            bin2hex(substr($bytes, 10, 6)),
        );
    }
}
