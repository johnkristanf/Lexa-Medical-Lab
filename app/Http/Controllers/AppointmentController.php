<?php

namespace App\Http\Controllers;

use App\Models\Appointments;
use App\Models\AppointmentSchedule;
use App\Models\TestCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Mail\AppointmentConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index()
    {
        $testCategories = TestCategory::with('testTypes')->get();
        $schedules = AppointmentSchedule::select('id', 'schedule')
            ->where('status', '=', 'available')
            ->get();

        return Inertia::render('Appointment/Index', [
            'test_categories' => $testCategories,
            'appointment_schedules' => $schedules,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'         => 'required|string|max:255',
            'middle_name'        => 'nullable|string|max:255',
            'last_name'          => 'required|string|max:255',
            'email'              => 'nullable|email|max:255', // nullable for those who don't want to give it
            'gender'             => 'required|string',
            'birthdate'          => 'required|date',
            'selected_schedule'  => 'required|exists:appointment_schedules,id',
            'selected_type_ids'  => 'required|array|min:1',
            'selected_type_ids.*' => 'exists:test_types,id',
        ]);


        $appointment = Appointments::create([
            'first_name'     => $validated['first_name'],
            'middle_name'    => $validated['middle_name'],
            'last_name'      => $validated['last_name'],
            'email'          => $validated['email'],
            'gender'         => $validated['gender'],
            'date_of_birth'  => $validated['birthdate'],
            'status'         => 'pending',
            'schedule_id'    => $validated['selected_schedule'],
        ]);

        Mail::to($appointment->email)->queue(new AppointmentConfirmationMail($appointment));
        return back()->with('success', 'Confirmation email sent.');

        $appointment->test_types()->attach($validated['selected_type_ids']);
        $selectedSchedule = AppointmentSchedule::where('id', $appointment->schedule_id)->value('schedule');

        return redirect()
            ->back()
            ->with([
                'success' => 'Successful Queue Insertion!',
                'schedule' => $selectedSchedule
            ]);
    }

    public function renderAdminAppointments()
    {
        $appointments = Appointments::with(['schedule', 'test_types'])
            ->latest()
            ->get();

        $schedules = AppointmentSchedule::select('id', 'schedule', 'status')
            ->latest()
            ->get();

        return Inertia::render('Admin/Appointments', [
            'appointments' => $appointments,
            'schedules' => $schedules,
        ]);
    }

    public function updateStatus(Appointments $appointment, Request $request)
    {
        $request->validate([
            'status' => 'required|string|in:pending,arrived,cancelled',
        ]);

        $appointment->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Status updated successfully.');
    }

    public function updateScheduleStatus(AppointmentSchedule $schedule, Request $request)
    {
        Log::info("sdfdsf");
        $request->validate([
            'status' => 'required|string|in:available,unavailable',
        ]);

        Log::info("status: " . $request->status);

        $schedule->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Schedule status updated successfully.');
    }

    public function sendEmail(Request $request)
    {
        $data = $request->only(['appointment_number', 'schedule', 'message']);

        // Replace with actual recipient email
        $recipient = 'example@domain.com';

        Mail::to($recipient)->send(new AppointmentConfirmationMail($data));

        return back()->with('success', 'Email sent.');
    }
}
