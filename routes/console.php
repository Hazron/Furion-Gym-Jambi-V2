<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
        $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:cek-member-masa-aktif')
        ->timezone('Asia/Jakarta');

Schedule::command('app:kirim-broadcast-member')
        ->dailyAt('09:00')
        ->timezone('Asia/Jakarta');

Schedule::command('promo:nonaktifkan')
        ->dailyAt('00:01');