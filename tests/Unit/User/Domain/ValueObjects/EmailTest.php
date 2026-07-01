<?php

declare(strict_types=1);

namespace Tests\Unit\User\Domain\ValueObjects;

use InvalidArgumentException;
use Modules\User\Domain\ValueObjects\Email;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    #[Test]
    public function it_creates_a_valid_email(): void
    {
        $email = Email::fromString('user@example.com');

        $this->assertSame('user@example.com', $email->toString());
        $this->assertSame('user@example.com', (string) $email);
    }

    #[Test]
    public function it_normalizes_email_to_lowercase(): void
    {
        $email = Email::fromString('User@Example.COM');

        $this->assertSame('user@example.com', $email->toString());
    }

    #[Test]
    public function it_trims_whitespace(): void
    {
        $email = Email::fromString('  user@example.com  ');

        $this->assertSame('user@example.com', $email->toString());
    }

    #[Test]
    #[DataProvider('invalidEmailProvider')]
    public function it_rejects_invalid_emails(string $invalidEmail): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address');

        Email::fromString($invalidEmail);
    }

    #[Test]
    public function it_rejects_empty_email(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Email::fromString('');
    }

    #[Test]
    public function two_emails_with_same_value_are_equal(): void
    {
        $email1 = Email::fromString('user@example.com');
        $email2 = Email::fromString('user@example.com');

        $this->assertTrue($email1->equals($email2));
    }

    #[Test]
    public function two_emails_with_different_values_are_not_equal(): void
    {
        $email1 = Email::fromString('user1@example.com');
        $email2 = Email::fromString('user2@example.com');

        $this->assertFalse($email1->equals($email2));
    }

    #[Test]
    public function equality_is_case_insensitive(): void
    {
        $email1 = Email::fromString('User@Example.com');
        $email2 = Email::fromString('user@example.com');

        $this->assertTrue($email1->equals($email2));
    }

    #[Test]
    public function it_returns_the_domain_part(): void
    {
        $email = Email::fromString('user@example.com');

        $this->assertSame('example.com', $email->domain());
    }

    #[Test]
    public function it_returns_the_local_part(): void
    {
        $email = Email::fromString('user@example.com');

        $this->assertSame('user', $email->localPart());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidEmailProvider(): iterable
    {
        yield 'missing at sign' => ['userexample.com'];
        yield 'missing domain' => ['user@'];
        yield 'missing local part' => ['@example.com'];
        yield 'spaces in middle' => ['user @example.com'];
        yield 'double at sign' => ['user@@example.com'];
        yield 'missing tld' => ['user@example'];
        yield 'special characters' => ['user<>@example.com'];
    }
}
