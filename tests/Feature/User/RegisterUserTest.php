<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    private const REGISTER_ENDPOINT = '/api/v1/users/register';

    #[Test]
    public function it_registers_a_user_with_valid_data(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'SecureP@ssw0rd!',
            'password_confirmation' => 'SecureP@ssw0rd!',
        ];

        $response = $this->postJson(self::REGISTER_ENDPOINT, $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                ],
            ])
            ->assertJsonFragment([
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
            ])
            ->assertJsonMissing([
                'password',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
            'name' => 'John Doe',
        ]);
    }

    #[Test]
    public function it_rejects_registration_without_required_fields(): void
    {
        $response = $this->postJson(self::REGISTER_ENDPOINT, []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    #[Test]
    public function it_rejects_registration_with_invalid_email(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'not-an-email',
            'password' => 'SecureP@ssw0rd!',
            'password_confirmation' => 'SecureP@ssw0rd!',
        ];

        $response = $this->postJson(self::REGISTER_ENDPOINT, $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function it_rejects_registration_with_duplicate_email(): void
    {
        // Register the first user
        $payload = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'SecureP@ssw0rd!',
            'password_confirmation' => 'SecureP@ssw0rd!',
        ];

        $this->postJson(self::REGISTER_ENDPOINT, $payload);

        // Attempt to register with the same email
        $duplicatePayload = [
            'name' => 'Jane Doe',
            'email' => 'john.doe@example.com',
            'password' => 'AnotherP@ssw0rd!',
            'password_confirmation' => 'AnotherP@ssw0rd!',
        ];

        $response = $this->postJson(self::REGISTER_ENDPOINT, $duplicatePayload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function it_rejects_registration_with_short_password(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ];

        $response = $this->postJson(self::REGISTER_ENDPOINT, $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    #[Test]
    public function it_rejects_registration_when_passwords_do_not_match(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'SecureP@ssw0rd!',
            'password_confirmation' => 'DifferentPassword!',
        ];

        $response = $this->postJson(self::REGISTER_ENDPOINT, $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    #[Test]
    public function it_rejects_registration_with_excessively_long_name(): void
    {
        $payload = [
            'name' => str_repeat('a', 256),
            'email' => 'john.doe@example.com',
            'password' => 'SecureP@ssw0rd!',
            'password_confirmation' => 'SecureP@ssw0rd!',
        ];

        $response = $this->postJson(self::REGISTER_ENDPOINT, $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    #[Test]
    public function it_trims_whitespace_from_name_and_email(): void
    {
        $payload = [
            'name' => '  John Doe  ',
            'email' => '  john.doe@example.com  ',
            'password' => 'SecureP@ssw0rd!',
            'password_confirmation' => 'SecureP@ssw0rd!',
        ];

        $response = $this->postJson(self::REGISTER_ENDPOINT, $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
            ]);
    }

    #[Test]
    public function it_normalizes_email_to_lowercase(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'John.Doe@Example.COM',
            'password' => 'SecureP@ssw0rd!',
            'password_confirmation' => 'SecureP@ssw0rd!',
        ];

        $response = $this->postJson(self::REGISTER_ENDPOINT, $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
        ]);
    }

    #[Test]
    public function it_returns_json_content_type(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'SecureP@ssw0rd!',
            'password_confirmation' => 'SecureP@ssw0rd!',
        ];

        $response = $this->postJson(self::REGISTER_ENDPOINT, $payload);

        $response->assertHeader('Content-Type', 'application/json');
    }
}
