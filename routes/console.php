<?php

use Illuminate\Foundation\Inspiring;    /* แสดงข้อความเมื่อถูกเรียกใช้ผ่าน php artisan inspir  ถูกรันโดยอัตโนมัติทุกชั่วโมง หากมีการตั้งค่า scheduler ไว้  */
use Illuminate\Support\Facades\Artisan;
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
