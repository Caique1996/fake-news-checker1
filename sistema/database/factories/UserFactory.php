<?php

namespace Database\Factories;

use App\Enums\BoolStatus;
use App\Enums\UserType;
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
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => \Hash::make('123456'), // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function isAdmin()
    {
        return $this->state(fn(array $attributes) => [
            'type' => UserType::Admin,
        ]);
    }

    public function isModerator()
    {
        return $this->state(fn(array $attributes) => [
            'type' => UserType::Moderator,
        ]);
    }

    public function isSubscriber()
    {
        return $this->state(fn(array $attributes) => [
            'type' => UserType::Subscriber,
        ]);
    }

    public function isActive()
    {
        return $this->state(fn(array $attributes) => [
            'status' => BoolStatus::Active,
        ]);
    }

    public function isInactive()
    {
        return $this->state(fn(array $attributes) => [
            'status' => BoolStatus::Active,
        ]);
    }
}
