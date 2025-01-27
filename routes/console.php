<?php

use App\Jobs\SendMessageJob;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Schedule::job(new SendMessageJob)->everyMinute();