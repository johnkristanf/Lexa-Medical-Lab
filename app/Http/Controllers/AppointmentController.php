<?php

namespace App\Http\Controllers;

use App\Events\QueueUpdate;
use App\Http\Requests\StoreAppoinmentScheduleRequest;
use App\Http\Requests\StoreAppointmentRequest;
use App\Mail\AppointmentConfirmationMail;
use App\Models\Appointments;
use App\Models\AppointmentSchedule;
use App\Models\AppointmentSlots;
use App\Models\Patient;
use App\Models\PriorityTypes;
use App\Models\Queues;
use App\Models\TestCategory;
use App\Services\QueueService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class AppointmentController extends Controller
{

    public function __construct(protected QueueService $queueService){}

    public function index()
    {
        $testCategories = TestCategory::with('testTypes')
            ->latest()
            ->get();

        $schedules = AppointmentSchedule::select('id', 'date')
            ->with(['appointment_slots'])
            ->latest()
            ->get();

        $priorityTypes = PriorityTypes::select('id', 'name', 'code')->get();


        return Inertia::render('Appointment/Index', [
            'test_categories' => $testCategories,
            'appointment_schedules' => $schedules,
            'priority_types' => $priorityTypes
        ]);
    }

    public function store(StoreAppointmentRequest $request)
    {
        Log::info('request', [$request]);

        $validated = $request->validated();
        Log::info('VALIDATE', [$validated]);

        DB::transaction(function () use ($validated) {
            $appointment = Appointments::create([
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],

                'gender' => $validated['gender']['name'],
                'priority_id' => $validated['priority_type']['id'],

                'date_of_birth' => $validated['birthdate'],
                'status' => 'pending',
                'schedule_id' => $validated['selected_schedule_id'],
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

    public function renderAdminAppointments(Request $request)
    {
        $searchQuery = $request->query('search');
        $appointments = Appointments::with(['schedule', 'test_types.test_category'])
            ->when($searchQuery, function ($query) use ($searchQuery) {
                $query->whereRaw(
                    "LOWER(CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name)) LIKE ?",
                    ['%'.strtolower($searchQuery).'%']
                )
                    ->orWhereRaw(
                        'LOWER(email) LIKE ?',
                        ['%'.strtolower($searchQuery).'%']
                    );
            })
            ->latest()
            ->get();

        $schedules = AppointmentSchedule::with(['appointment_slots'])
            ->latest()
            ->get();

        return Inertia::render('Admin/Appointments', [
            'appointments' => $appointments,
            'schedules' => $schedules,
        ]);
    }

    public function updateStatus(Appointments $appointment, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,arrived',
            'patient_id' => 'sometimes|string',
            'first_name' => 'sometimes|string',
            'middle_name' => 'sometimes|nullable|string',
            'last_name' => 'sometimes|string',
            'email' => 'sometimes|nullable|email',
            'phone' => 'sometimes|nullable|string',
            'address' => 'sometimes|nullable|string',
            'gender' => 'sometimes|string',
            'date_of_birth' => 'sometimes|date|date_format:Y-m-d',
        ]);

        Log::info('appointment: ', [$appointment]);
        Log::info('validated: ', [$validated]);

        DB::transaction(function () use ($validated, $appointment) {
            // Fix: Properly retrieve the PriorityTypes model by its primary key using findOrFail
            $priorityType = PriorityTypes::findOrFail($appointment->priority_id);

            // Create a new Patient record using the validated data
            Patient::create([
                'patient_id' => $validated['patient_id'] ?? '',

                'first_name' => $validated['first_name'] ?? '',
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'] ?? '',

                'gender' => $validated['gender'] ?? '',
                'date_of_birth' => $validated['date_of_birth'] ?? '',

                'address' => $validated['address'] ?? '',
                'contact_number' => $validated['phone'] ?? '',
                'email' => $validated['email'] ?? null,

                'priority_id' => $priorityType->id,
                'transaction_type' => Patient::APPOINMENT
            ]);

            // MAGKA PROBLEMA SIYA PAG WLA PAJUY UNOD ANG QUEUE MAG FAIL ANG WHERE CONDITION
            $queueNumber = $this->queueService->getNewQueueNumber($priorityType->id);

            if ($queueNumber === null) {
                // If there are no existing queues, default to "01" using the priority type's code
                $queueNumber = '01';
            }

            $formmattedQueueNumber = $priorityType->code . '-' . $queueNumber;

            // Add the appointment patient to the queue
            $queue = Queues::create([
                'queue_number' => $formmattedQueueNumber,
                'priority_type_id' => $appointment->priority_id,
                'status_id' => 1, // default status is "waiting"
                'is_appointment' => true,
                'appointment_number' => $appointment->appointment_number,
            ]);

            if ($queue) {
                broadcast(new QueueUpdate($queue->id));
            }

            // DELETE EXISTING APPOINTMENT
            $appointment->delete();
        });

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
        Log::info('data', [$data]);

        $recipient = $data['email'];
        Mail::to($recipient)->send(new AppointmentConfirmationMail($data));

        Appointments::where('id', $data['appointment_id'])->update([
            'appointment_number' => $data['appointment_number'],
        ]);

        return redirect()
            ->back()
            ->with('success', 'Appointment Email Sent Successfully');
    }

    public function addAppointmentSchedule(StoreAppoinmentScheduleRequest $request)
    {
        $data = $request->validated();

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
