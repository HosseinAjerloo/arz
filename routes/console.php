<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use \App\Jobs\VoucherBankArrangementJob;
use \App\Jobs\transmissionsBankArrangementJob;
\Illuminate\Support\Facades\Schedule::command('exchange-rate-update')->runInBackground()->hourly()->withoutOverlapping();

\Illuminate\Support\Facades\Schedule::command('queue:work --stop-when-empty --queue BuyUtopiaCouponsWithJob')->runInBackground()->everyFiveMinutes();
