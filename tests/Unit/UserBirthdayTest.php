<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserBirthdayTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_birthday_returns_true_when_born_today()
    {
        $today = Carbon::today();
        $user = User::factory()->create([
            'birth_date' => $today->copy()->subYears(22)->format('Y-m-d'),
        ]);

        $this->assertTrue($user->is_birthday);
        $this->assertEquals(22, $user->age);
    }

    public function test_is_birthday_returns_false_when_born_on_different_day()
    {
        $user = User::factory()->create([
            'birth_date' => Carbon::today()->addDays(2)->subYears(21)->format('Y-m-d'),
        ]);

        $this->assertFalse($user->is_birthday);
    }

    public function test_is_birthday_returns_false_when_birth_date_is_null()
    {
        $user = User::factory()->create([
            'birth_date' => null,
        ]);

        $this->assertFalse($user->is_birthday);
        $this->assertNull($user->age);
    }

    public function test_scope_birthday_today_retrieves_only_today_celebrants()
    {
        $today = Carbon::today();

        // User with birthday today
        $celebrant = User::factory()->create([
            'name' => 'Celebrant User',
            'birth_date' => $today->copy()->subYears(23)->format('Y-m-d'),
        ]);

        // User with birthday tomorrow
        $tomorrowUser = User::factory()->create([
            'name' => 'Tomorrow User',
            'birth_date' => $today->copy()->addDay()->subYears(20)->format('Y-m-d'),
        ]);

        // User without birth_date
        $noBirthdayUser = User::factory()->create([
            'name' => 'No Birthday User',
            'birth_date' => null,
        ]);

        $results = User::birthdayToday()->get();

        $this->assertTrue($results->contains($celebrant));
        $this->assertFalse($results->contains($tomorrowUser));
        $this->assertFalse($results->contains($noBirthdayUser));
    }
}
