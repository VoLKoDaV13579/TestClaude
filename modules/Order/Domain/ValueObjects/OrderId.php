<?php

declare(strict_types=1);

namespace Modules\Order\Domain\ValueObjects;

use Modules\Shared\Domain\ValueObjects\Uuid;

final readonly class OrderId extends Uuid
{
}
