<?php

namespace Sagautam5\EmailBlocker\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Sagautam5\EmailBlocker\Enums\ReceiverType;
use Sagautam5\EmailBlocker\Models\BlockedEmail;

/**
 * @extends Factory<BlockedEmail>
 */
class BlockedEmailFactory extends Factory
{
    protected $model = BlockedEmail::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        return [
            'mailable'   => $this->faker->randomElement([
                'App\\Mail\\TestMail',
                'App\\Mail\\AnotherMail',
                'App\\Mail\\NotificationMail',
            ]),
            'email'      => $this->faker->safeEmail(),
            'subject'    => $this->faker->sentence(),
            'from_name'  => $this->faker->name(),
            'from_email' => $this->faker->safeEmail(),
            'content'    => $this->faker->paragraph(),
            'rule'       => 'TestRule',
            'receiver_type' => $this->faker->randomElement([
                ReceiverType::CC,
                ReceiverType::BCC,
                ReceiverType::TO,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * State: null mailable (useful for exclusion tests)
     */
    public function withoutMailable(): self
    {
        return $this->state(fn () => [
            'mailable' => null,
        ]);
    }
}
