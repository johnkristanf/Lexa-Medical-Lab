<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppoinmentScheduleRequest;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointments;
use App\Models\AppointmentSchedule;
use App\Models\TestCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Mail\AppointmentConfirmationMail;
use App\Models\AppointmentSlots;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function index()
    {
        $testCategories = TestCategory::with('testTypes')->get();
        $schedules = AppointmentSchedule::select('id', 'date')
            ->with(['appointment_slots'])
            ->get();

        return Inertia::render('Appointment/Index', [
            'test_categories' => $testCategories,
            'appointment_schedules' => $schedules,
        ]);
    }

    public function store(StoreAppointmentRequest $request)
    {

        $validated = $request->validated();
        Log::info("validated appointmebnt data", [$validated]);

        DB::transaction(function () use ($validated) {
            $appointment = Appointments::create([
                'first_name'     => $validated['first_name'],
                'middle_name'    => $validated['middle_name'],
                'last_name'      => $validated['last_name'],
                'email'          => $validated['email'],
                'gender'         => $validated['gender'],
                'date_of_birth'  => $validated['birthdate'],
                'status'         => 'pending',
                'schedule_id'    => $validated['selected_schedule_id'],
            ]);

            // MAKE THE SELECTED TIME SLOT TO UNAVAILABLE
            $appointmentSlot = AppointmentSlots::findOrFail($validated['selected_time_slot_id']);
            $appointmentSlot->update(['status' => AppointmentSlots::UNAVAIALBLE]);

            $appointment->test_types()->attach($validated['selected_type_ids']);

            // ✅ Will only queue after DB commit
            Mail::to($appointment->email)
                ->queue(new AppointmentConfirmationMail($appointment));
        });



        return redirect()
            ->back()
            ->with([
                'success' => 'Successful Appointment Schedule!',
            ]);
    }

    public function renderAdminAppointments()
    {
        $appointments = Appointments::with(['schedule', 'test_types.test_category'])
            ->latest()
            ->get();

        $schedules = AppointmentSchedule::with(['appointment_slots'])
            ->latest()
            ->get();

        Log::info("appointments admin: ", [$appointments]);
        Log::info("schedules admin: ", [$schedules]);

        return Inertia::render('Admin/Appointments', [
            'appointments' => $appointments,
            'schedules' => $schedules,
        ]);
    }

    public function updateStatus(Appointments $appointment, Request $request)
    {
        $request->validate([
            'status' => 'required',
        ]);

        $appointment->status = $request->status;
        $appointment->save();

        return back()->with('success', 'Status updated successfully.');
    }

    public function updateScheduleStatus($slotId, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:available,unavailable',
        ]);

        $appointmentSlot = AppointmentSlots::findOrFail($slotId);
        $appointmentSlot->update(['status' => $validated['status']]);

        return back()->with('success', 'Schedule status updated successfully.');
    }

    public function sendEmailDetails(Request $request)
    {
        $data = $request->only(['appointment_id', 'email', 'appointment_number', 'schedule', 'message']);
        Log::info("data", [$data]);
        
        $recipient = $data['email'];
        Mail::to($recipient)->send(new AppointmentConfirmationMail($data));

        Appointments::where('id', $data['appointment_id'])->update([
            'appointment_number' => $data['appointment_number']
        ]);
    }

    public function addAppointmentSchedule(StoreAppoinmentScheduleRequest $request)
    {
        $data = $request->validated();
        Log::info("Schedule data: ", [$data]);

        DB::transaction(function () use ($data) {
            // Create parent schedule
            $newSchedule = AppointmentSchedule::create([
                'date' => Carbon::parse($data['date']),
            ]);

            // Create child slots
            foreach ($data['timeSlots'] as $slot) {
                AppointmentSlots::create([
                    'time_slot' => $slot['time'],
                    'status' => $slot['status'],
                    'schedule_id' => $newSchedule->id,
                ]);
            }
        });

        return redirect()
            ->route('admin.appointments')
            ->with('success', 'Schedule created successfully!');
    }
}
