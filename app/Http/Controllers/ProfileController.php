<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [  /**ใช้สำหรับแสดงหน้าแก้ไขข้อมูลโปรไฟล์ (Profile/Edit)
                                                  //ข้อมูลที่ส่งไปยังหน้า Inertia
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,  /**mustVerifyEmail:
                                                                                ตรวจสอบว่าผู้ใช้ที่ล็อกอินอยู่เป็นอินสแตนซ์ของ MustVerifyEmail หรือไม่
                                                                                ใช้สำหรับกำหนดว่าผู้ใช้ต้องยืนยันอีเมลหรือไม่ */

            'status' => session('status'), /**ดึงข้อความสถานะ (ถ้ามี) จากเซสชัน เช่น การอัปเดตโปรไฟล์สำเร็จ */
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());     /**ตรวจสอบความถูกต้องของข้อมูล:
                                                            ใช้ ProfileUpdateRequest (Request Class) เพื่อกำหนด validation rules
                                                            ตรวจสอบข้อมูลที่ส่งมาจากฟอร์ม เช่น ชื่อ, อีเมล, หรือข้อมูลอื่นๆ */

        if ($request->user()->isDirty('email')) { /**เช็คว่ามีการเปลี่ยนแปลงอีเมลหรือไม่ (isDirty('email')):
                                                หากมีการเปลี่ยนแปลง ให้รีเซ็ตค่าการยืนยันอีเมล (email_verified_at = null) เพื่อบังคับให้ผู้ใช้ยืนยันอีเมลใหม่ */

            $request->user()->email_verified_at = null;
        }

        $request->user()->save();  /**บันทึกข้อมูลลงฐานข้อมูลด้วย $request->user()->save()
        */

        return Redirect::route('profile.edit'); /**ปลี่ยนเส้นทาง:
        ใช้ Redirect::route('profile.edit') เพื่อกลับไปยังหน้าแก้ไขโปรไฟล์  */
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],   /**ตรวจสอบรหัสผ่าน:
                                                    ใช้ $request->validate() เพื่อเช็คว่ารหัสผ่านที่ผู้ใช้ป้อนตรงกับรหัสผ่านปัจจุบันหรือไม่ (current_password) */
        ]);

        $user = $request->user(); /**ลบบัญชีผู้ใช้:  ใช้ $request->user() เพื่อดึงข้อมูลของผู้ใช้ที่ล็อกอิน*/

        Auth::logout(); /**เรียก Auth::logout() เพื่อออกจากระบบ */

        $user->delete(); /**ลบบัญชีผู้ใช้ด้วย $user->delete() */

        /**จัดการเซสชัน */
        $request->session()->invalidate(); /**เรียก $request->session()->invalidate() เพื่อทำให้เซสชันหมดอายุ */
        $request->session()->regenerateToken(); /**ร้าง Token ใหม่ด้วย $request->session()->regenerateToken() */


        /**เปลี่ยนเส้นทาง */
        return Redirect::to('/'); /**ใช้ Redirect::to('/') เพื่อเปลี่ยนไปยังหน้าแรก */
    }
}
