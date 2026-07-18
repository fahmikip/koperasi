<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Member> */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        return ['member_number' => 'KOP-'.now()->year.'-'.$this->faker->unique()->numerify('######'), 'nik' => $this->faker->unique()->numerify('################'), 'name' => $this->faker->name(), 'birth_place' => $this->faker->city(), 'birth_date' => $this->faker->dateTimeBetween('-60 years', '-18 years'), 'gender' => $this->faker->randomElement(['male', 'female']), 'address' => $this->faker->address(), 'district' => $this->faker->citySuffix(), 'regency' => $this->faker->city(), 'province' => $this->faker->state(), 'whatsapp' => '08'.$this->faker->numerify('##########'), 'email' => $this->faker->unique()->safeEmail(), 'occupation' => $this->faker->jobTitle(), 'joined_at' => now(), 'valid_until' => now()->addYears(5), 'status' => 'active', 'qr_token' => (string) Str::uuid()];
    }
}
