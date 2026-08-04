<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('subscriptions:expire')->hourly()->withoutOverlapping();
Schedule::command('subscriptions:send-renewal-reminders')->dailyAt('08:00');
