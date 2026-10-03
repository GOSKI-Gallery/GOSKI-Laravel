<?php

namespace Tests\Unit;

use App\Helpers\TimeHelper;
use Carbon\Carbon;
use Tests\TestCase;

class TimeHelperTest extends TestCase
{
    public function test_time_ago_returns_agora_mesmo_for_very_recent(): void
    {
        $now = Carbon::now()->toIso8601String();
        $this->assertEquals('agora mesmo', TimeHelper::timeAgo($now));

        $fewSecondsAgo = Carbon::now()->subSeconds(10)->toIso8601String();
        $this->assertEquals('agora mesmo', TimeHelper::timeAgo($fewSecondsAgo));

        $almostMinute = Carbon::now()->subSeconds(29)->toIso8601String();
        $this->assertEquals('agora mesmo', TimeHelper::timeAgo($almostMinute));
    }

    public function test_time_ago_returns_1_minuto_at_30_seconds(): void
    {
        $thirtySeconds = Carbon::now()->subSeconds(30)->toIso8601String();
        $this->assertEquals('há 1 minuto', TimeHelper::timeAgo($thirtySeconds));

        $fiftyNineSeconds = Carbon::now()->subSeconds(59)->toIso8601String();
        $this->assertEquals('há 1 minuto', TimeHelper::timeAgo($fiftyNineSeconds));
    }

    public function test_time_ago_returns_minutes_for_under_an_hour(): void
    {
        $twoMinutes = Carbon::now()->subMinutes(2)->toIso8601String();
        $this->assertEquals('há 2 minutos', TimeHelper::timeAgo($twoMinutes));

        $thirtyMinutes = Carbon::now()->subMinutes(30)->toIso8601String();
        $this->assertEquals('há 30 minutos', TimeHelper::timeAgo($thirtyMinutes));

        $fiftyNineMinutes = Carbon::now()->subMinutes(59)->toIso8601String();
        $this->assertEquals('há 59 minutos', TimeHelper::timeAgo($fiftyNineMinutes));
    }

    public function test_time_ago_returns_1_hora_at_60_minutes(): void
    {
        $oneHour = Carbon::now()->subHour()->toIso8601String();
        $this->assertEquals('há 1 hora', TimeHelper::timeAgo($oneHour));

        $twoHours = Carbon::now()->subHours(2)->toIso8601String();
        $this->assertEquals('há 2 horas', TimeHelper::timeAgo($twoHours));

        $twentyThreeHours = Carbon::now()->subHours(23)->toIso8601String();
        $this->assertEquals('há 23 horas', TimeHelper::timeAgo($twentyThreeHours));
    }

    public function test_time_ago_returns_1_dia_at_24_hours(): void
    {
        $oneDay = Carbon::now()->subDay()->toIso8601String();
        $this->assertEquals('há 1 dia', TimeHelper::timeAgo($oneDay));

        $threeDays = Carbon::now()->subDays(3)->toIso8601String();
        $this->assertEquals('há 3 dias', TimeHelper::timeAgo($threeDays));

        $sixDays = Carbon::now()->subDays(6)->toIso8601String();
        $this->assertEquals('há 6 dias', TimeHelper::timeAgo($sixDays));
    }

    public function test_time_ago_returns_1_semana_at_7_days(): void
    {
        $oneWeek = Carbon::now()->subWeek()->toIso8601String();
        $this->assertEquals('há 1 semana', TimeHelper::timeAgo($oneWeek));

        $twoWeeks = Carbon::now()->subWeeks(2)->toIso8601String();
        $this->assertEquals('há 2 semanas', TimeHelper::timeAgo($twoWeeks));

        $threeWeeks = Carbon::now()->subWeeks(3)->toIso8601String();
        $this->assertEquals('há 3 semanas', TimeHelper::timeAgo($threeWeeks));
    }

    public function test_time_ago_returns_1_mes_at_4_weeks(): void
    {
        $oneMonth = Carbon::now()->subMonth()->toIso8601String();
        $this->assertEquals('há 1 mês', TimeHelper::timeAgo($oneMonth));

        $threeMonths = Carbon::now()->subMonths(3)->toIso8601String();
        $this->assertEquals('há 3 meses', TimeHelper::timeAgo($threeMonths));

        $elevenMonths = Carbon::now()->subMonths(11)->toIso8601String();
        $this->assertEquals('há 11 meses', TimeHelper::timeAgo($elevenMonths));
    }

    public function test_time_ago_returns_1_ano_at_12_months(): void
    {
        $oneYear = Carbon::now()->subYear()->toIso8601String();
        $this->assertEquals('há 1 ano', TimeHelper::timeAgo($oneYear));

        $twoYears = Carbon::now()->subYears(2)->toIso8601String();
        $this->assertEquals('há 2 anos', TimeHelper::timeAgo($twoYears));

        $fiveYears = Carbon::now()->subYears(5)->toIso8601String();
        $this->assertEquals('há 5 anos', TimeHelper::timeAgo($fiveYears));
    }

    public function test_time_ago_handles_invalid_date_gracefully(): void
    {
        $this->assertEquals('agora mesmo', TimeHelper::timeAgo('invalid-date'));
        $this->assertEquals('agora mesmo', TimeHelper::timeAgo(''));
        $this->assertEquals('agora mesmo', TimeHelper::timeAgo('not-a-date'));
    }

    public function test_time_ago_handles_future_dates(): void
    {
        $future = Carbon::now()->addHour()->toIso8601String();
        $this->assertEquals('agora mesmo', TimeHelper::timeAgo($future));
    }
}
