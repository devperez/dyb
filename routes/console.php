<?php

use App\Jobs\SendMessageJob;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Schedule::job(new SendMessageJob)->everyMinute();

// Artisan::command('schedule:run', function () {
//     $schedule = app(Schedule::class);
//     $schedule->job(new SendMessageJob, 'default')->everyMinute();
// })->describe('Run the scheduled jobs');