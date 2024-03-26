<?php

namespace App\Services;

use App\Models\Setting;
use Twilio\Rest\Client;

class TwilioService {
    public function sendWhatsAppNotification($phone, $message)
    {
        $setting = Setting::first();
        $sid = $setting->twilio_acc_id;
        $token = $setting->twilio_auth_token;

        try {
            $client = new Client($sid, $token);
            $client->messages->create(
                'whatsapp:' . $phone,
                [
                    'from' => 'whatsapp:' . $setting->twilio_phone_no,
                    'body' => $message
                ]
            );
        } catch (\Throwable $th) {
            // Handle exception if required
            \Log::error($th->getMessage());
        }
    }

}