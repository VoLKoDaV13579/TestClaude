<?php

declare(strict_types=1);

namespace Tests\Unit\Order\Domain\ValueObjects;

use InvalidArgumentException;
use Modules\Order\Domain\ValueObjects\OrderId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class OrderIdTest extends TestCase
{
    #[Test]
    public function it_creates_an_order_id_from_a_valid_uuid(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $orderId = OrderId::fromString($uuid);

        $this->assertSame($uuid, $orderId->toString());
        $this->assertSame($uuid, (string) $orderId);
    }

    #[Test]
    public function it_generates_a_new_order_id(): void
    {
        $orderId = OrderId::generate();

        $this->assertTrue(Uuid::isValid($orderId->toString()));
    }

    #[Test]
    public function it_rejects_invalid_uuid_format(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid OrderId');

        OrderId::fromString('not-a-valid-uuid');
    }

    #[Test]
    public function it_rejects_empty_string(): void
    {
        $this->expectException(InvalidArgumentException::class);

        OrderId::fromString('');
    }

    #[Test]
    public function two_order_ids_with_same_value_are_equal(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $orderId1 = OrderId::fromString($uuid);
        $orderId2 = OrderId::fromString($uuid);

        $this->assertTrue($orderId1->equals($orderId2));
    }

    #[Test]
    public function two_order_ids_with_different_values_are_not_equal(): void
    {
        $orderId1 = OrderId::generate();
        $orderId2 = OrderId::generate();

        $this->assertFalse($orderId1->equals($orderId2));
    }

    #[Test]
    public function generated_order_ids_are_unique(): void
    {
        $ids = [];

        for ($i = 0; $i < 100; $i++) {
            $ids[] = OrderId::generate()->toString();
        }

        $this->assertCount(100, array_unique($ids));
    }

    #[Test]
    public function it_is_case_insensitive_for_uuid_comparison(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $orderId1 = OrderId::fromString(mb_strtolower($uuid));
        $orderId2 = OrderId::fromString(mb_strtoupper($uuid));

        $this->assertTrue($orderId1->equals($orderId2));
    }
}
