
<h2>Appointment Confirmation</h2>

{{-- <p>Dear {{ $appointment->first_name }} {{ $appointment->last_name }},</p> --}}
{{-- <p>Dear {{ $appointment->first_name }} </p> --}}


<p>Your appointment has been successfully booked.</p>

<ul>
    <h2>Appointment Confirmation</h2>
    <p><strong>Appointment Number:</strong> {{ $data['appointment_number'] }}</p>
    <p><strong>Schedule:</strong> {{ $data['schedule'] }}</p>
    <p>{{ $data['message'] }}</p>
</ul>

<p><strong>Please Read Carefully:</strong></p>
<ul>
    <li>Take note or screenshot your appointment code.</li>
    <li>Bring a valid Government-issued ID and your VACCINATION CARD.</li>
    <li>Prepare your Appointment Code upon arrival.</li>
    <li>No face mask, NO ENTRY.</li>
    <li>Arrive at least 30 minutes before your scheduled appointment.</li>
</ul>

<p>Thank you!</p>
