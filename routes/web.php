<?php

use App\Http\Controllers\ChirpController;                    /* */
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),                /*เส้นทางนี้กำหนดหน้าแรก (homepage) ของแอป
                                                            ใช้ Inertia.js ในการแสดงหน้า Welcome โดยส่งข้อมูลเพิ่มเติมไปยังหน้า:
                                                            canLogin และ canRegister ใช้เช็คว่ามี route สำหรับการเข้าสู่ระบบ (login) หรือการลงทะเบียน (register) หรือไม่
                                                            laravelVersion แสดงเวอร์ชันของ Laravel
                                                            phpVersion แสดงเวอร์ชันของ PHP ที่ใช้งานอยู่ */
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');                           /* เส้นทางนี้แสดงหน้า Dashboard โดย:
                                                                    ใช้ middleware auth และ verified เพื่อให้เฉพาะผู้ใช้ที่เข้าสู่ระบบและยืนยันอีเมลแล้วเท่านั้นที่เข้าถึงได้
                                                                    กำหนดชื่อให้ route ว่า dashboard                                          */
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');    /*เส้นทางในกลุ่มนี้จะต้องผ่าน middleware auth ซึ่งหมายความว่าผู้ใช้ต้องเข้าสู่ระบบก่อนจึงจะเข้าถึงได้ */
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('chirps', ChirpController::class)  /**กำหนดการจัดการเส้นทางสำหรับทรัพยากร chirps ผ่าน ChirpController โดยกำหนดให้ใช้เฉพาะ action: */
    ->only(['index', 'store', 'update', 'destroy'])
    ->middleware(['auth', 'verified']);

require __DIR__.'/auth.php';
