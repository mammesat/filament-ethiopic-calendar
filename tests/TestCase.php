<?php

declare(strict_types=1);

namespace Mammesat\FilamentEthiopicCalendar\Tests;

use Mammesat\FilamentEthiopicCalendar\EthiopicCalendarServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            EthiopicCalendarServiceProvider::class,
        ];
    }
}
