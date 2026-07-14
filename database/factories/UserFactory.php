<?php

namespace Database\Factories;

use App\Models\Oficina;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id'                        => $this->faker->unique()->numberBetween(100000, 999999), // PK manual: users.id no es autoincremental
            'name'                      => $this->faker->name(),
            'username'                  => $this->faker->unique()->userName(),
            'dui'                       => $this->faker->unique()->numerify('#########'),
            'email'                     => $this->faker->unique()->safeEmail(),
            'email_verified_at'         => now(),
            'password'                  => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role_id'                   => fn () => Role::findOrCreate('cliente', 'web')->id,
            'oficina_id'                => fn () => Oficina::first()?->id ?? Oficina::forceCreate(['id' => 1, 'oficina' => 'Oficina de pruebas', 'activo' => true])->id,
            'two_factor_secret'         => null,
            'two_factor_recovery_codes' => null,
            'remember_token'            => Str::random(10),
            'current_team_id'           => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Usuario desactivado (users.activo = false).
     */
    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'activo' => false,
        ]);
    }

    /**
     * Compatibilidad con tests de Jetstream: la feature de teams está
     * deshabilitada en config/jetstream.php, por lo que no crea nada.
     */
    public function withPersonalTeam(callable $callback = null): static
    {
        return $this->state([]);
    }

    /**
     * Usuario con 2FA habilitado y confirmado.
     */
    public function conDosFactores(): static
    {
        return $this->state(fn (array $attributes) => [
            'two_factor_secret'         => encrypt('test-secret'),
            'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-uno'])),
            'two_factor_confirmed_at'   => now(),
        ]);
    }
}
