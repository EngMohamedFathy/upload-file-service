<?php

use App\Jobs\CheckExpiredTokensJob;
use Illuminate\Support\Facades\Schedule;


// schedule to run job for check expired tokens and change status
Schedule::job(new CheckExpiredTokensJob())->everyTenMinutes();
