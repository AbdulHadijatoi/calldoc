<?php

namespace App\AppointmentReminders;

use App\Models\Appointment;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Log;

class AppointmentReminder
{
    public $appointments;
    protected $twilioService;
    /**
     * Construct a new AppointmentReminder
     *
     * @param Illuminate\Support\Collection $twilioClient The client to use to query the API
     */
    function __construct()
    {

        $this->appointments = Appointment::appointmentsWithin24Hours()->get();
        $this->twilioService = new TwilioService();
    }

    /**
     * Send reminders for each appointment
     *
     * @return void
     */
    public function sendReminders() {
        $this->appointments->each(
            function ($appointment) {
                $this->_sendMessage($appointment);
                $appointment->notification_sent = 1;
                $appointment->save();
            }
        );
    }

    /**
     * Sends a single message using the app's global configuration
     *
     * @param string $number  The number to message
     * @param string $content The content of the message
     *
     * @return void
     */
    private function _sendMessage($appointment)
    {
        Log::info('AppointmentsWithin24Hours Sending notification for appointment',[$appointment]); // Log message
        // TOMORROW APPOINTMENT TO PATIENT
        $user = $appointment->user;
        $doctor = $appointment->doctor;
        $hospital = $appointment->hospital;
        $hospitalAddress = $hospital?$hospital->address??null:null;
        if($hospitalAddress && $user){
            $lat = $hospital->lat;
            $long = $hospital->lng;
            $google_map_url = "https://www.google.com/maps?q=$lat,$long";
            $this->twilioService->sendContentTemplate($user->phone,'HX037303c350c06dada9fdcbfff4792c36',[
                "1" => $user->name,
                "2" => $appointment->date,
                "3" => $appointment->time,
                "4" => $doctor->name,
                "5" => $hospitalAddress,
                "6" => $doctor->user->phone_code . $doctor->user->phone,
                "7" => $google_map_url,
            ]);
        }
    }
}
