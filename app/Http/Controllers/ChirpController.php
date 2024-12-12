<?php

namespace App\Http\Controllers;

use App\Models\Chirp;
use Illuminate\Http\Request;
//use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
       // return response('Hello, World!');
       return Inertia::render('Chirps/Index', [
        //
        'chirps' => Chirp::with('user:id,name')->latest()->get(),
        ]);                                                                                  /**ใช้สำหรับแสดงรายการของ Chirp (โพสต์ทั้งหมด)
                                                                                                ใช้ Inertia.js เพื่อแสดงหน้า Chirps/Index
                                                                                                ส่งข้อมูล chirps ที่ดึงมาจากฐานข้อมูลไปยังหน้า:
                                                        ใช้ Chirp::with('user:id,name') เพื่อดึงข้อมูลของผู้ใช้ (user) ที่เกี่ยวข้อง (เฉพาะ id และ name)
                                                        ใช้ latest() เพื่อเรียงลำดับข้อมูลจากใหม่ไปเก่า
                                                        ใช้ get() เพื่อดึงข้อมูลทั้งหมด
                                                        ผลลัพธ์นี้จะแสดงโพสต์ทั้งหมดในหน้า Chirps/Index */
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([  /**ใช้ $request->validate() ตรวจสอบความถูกต้องของข้อมูลที่ส่งมา: */
            'message' => 'required|string|max:255', /**message เป็นข้อความ (string) ที่ต้องไม่เกิน 255 ตัวอักษรและต้องกรอก (required) */
        ]);

        $request->user()->chirps()->create($validated);
         /**ใช้ $request->user()->chirps()->create($validated):
        user() เป็นการอ้างอิงผู้ใช้ที่ล็อกอินอยู่
        chirps() เป็นความสัมพันธ์ (relationship) ระหว่างผู้ใช้และโพสต์
        create($validated) ใช้ข้อมูลที่ผ่านการตรวจสอบแล้วเพื่อบันทึกลงฐานข้อมูล */

        return redirect(route('chirps.index')); /**ใช้ redirect(route('chirps.index')) เพื่อเปลี่ยนกลับไปยังหน้ารายการโพสต์ */
    }

    /**
     * Display the specified resource.
     */
    public function show(Chirp $chirp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chirp $chirp)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chirp $chirp): RedirectResponse
    {
        Gate::authorize('update', $chirp); /**ใช้ Gate::authorize('update', $chirp) เพื่อเช็คว่าผู้ใช้ที่ล็อกอินอยู่มีสิทธิ์ในการอัปเดตโพสต์นี้หรือไม่ (โดยใช้ Policy) */

        $validated = $request->validate([  /**ใช้ $request->validate() ตรวจสอบความถูกต้องของข้อมูลที่ส่งมา*/
            'message' => 'required|string|max:255',
        ]);

        $chirp->update($validated); /**ใช้ $chirp->update($validated) เพื่อบันทึกข้อมูลใหม่ลงในฐานข้อมูล */

        return redirect(route('chirps.index')); /**ใช้ redirect(route('chirps.index')) เพื่อกลับไปยังหน้ารายการโพสต์ */
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chirp $chirp): RedirectResponse /**การกำหนดฟังก์ชัน: ฟังก์ชัน destroy ถูกกำหนดให้รับออบเจ็กต์ Chirp เป็นพารามิเตอร์และส่งกลับ RedirectResponse */
    {
        Gate::authorize('delete', $chirp); /**ตรวจสอบว่าผู้ใช้ปัจจุบันมีสิทธิ์ลบออบเจ็กต์ Chirp นี้หรือไม่ หากผู้ใช้ไม่มีสิทธิ์ จะมีการโยนข้อยกเว้นและฟังก์ชันจะไม่ทำงานต่อ */

        $chirp->delete(); /**หากการตรวจสอบสิทธิ์ผ่าน บรรทัดนี้จะลบออบเจ็กต์ Chirp ออกจากฐานข้อมูล */

        return redirect(route('chirps.index')); /**หลังจากลบ Chirp แล้ว ฟังก์ชันจะเปลี่ยนเส้นทางผู้ใช้ไปยังเส้นทางที่ชื่อ chirps.index ซึ่งโดยทั่วไปจะเป็นหน้าที่แสดงรายการ chirps ทั้งหมด */
    }
}
