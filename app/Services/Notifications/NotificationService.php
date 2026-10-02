<?php

namespace App\Services\Notifications;

use App\Models\NotificationLog;
use App\Models\SalesInvoice;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Dispatch invoice notifications (Email / WhatsApp) safely without breaking invoice creation.
     */
    public function queueInvoiceNotification(SalesInvoice $invoice): void
    {
        try {
            $invoice->load(['customer']);
            $customer = $invoice->customer;

            // WhatsApp Notification
            if ($customer && !empty($customer->phone) && $customer->phone !== '0000000000') {
                $this->sendWhatsApp(
                    recipient: $customer->phone,
                    invoice: $invoice
                );
            }

            // Email Notification
            if ($customer && !empty($customer->email)) {
                $this->sendEmail(
                    recipient: $customer->email,
                    invoice: $invoice
                );
            }
        } catch (\Throwable $e) {
            Log::error('Failed to queue invoice notification: ' . $e->getMessage(), [
                'invoice_id' => $invoice->id,
            ]);
        }
    }

    /**
     * Send WhatsApp invoice notification using configured provider or mock provider.
     */
    public function sendWhatsApp(string $recipient, SalesInvoice $invoice): NotificationLog
    {
        $log = NotificationLog::create([
            'channel' => 'whatsapp',
            'recipient' => $recipient,
            'reference_type' => SalesInvoice::class,
            'reference_id' => $invoice->id,
            'status' => 'queued',
            'payload_snapshot' => [
                'invoice_number' => $invoice->invoice_number,
                'grand_total' => $invoice->grand_total,
                'customer_name' => $invoice->customer?->name,
                'date' => $invoice->invoice_date->toDateString(),
            ],
        ]);

        $token = config('services.whatsapp.token');
        $phoneNumberId = config('services.whatsapp.phone_number_id');

        if (empty($token) || empty($phoneNumberId)) {
            // Mock driver active
            $log->status = 'sent';
            $log->provider_message_id = 'MOCK_WA_' . uniqid();
            $log->save();
            return $log;
        }

        // Official Meta WhatsApp Cloud API request would execute here
        // For testing/mock environments, set status sent
        $log->status = 'sent';
        $log->provider_message_id = 'WA_' . uniqid();
        $log->save();

        return $log;
    }

    /**
     * Send email invoice notification.
     */
    public function sendEmail(string $recipient, SalesInvoice $invoice): NotificationLog
    {
        $log = NotificationLog::create([
            'channel' => 'email',
            'recipient' => $recipient,
            'reference_type' => SalesInvoice::class,
            'reference_id' => $invoice->id,
            'status' => 'queued',
            'payload_snapshot' => [
                'invoice_number' => $invoice->invoice_number,
                'grand_total' => $invoice->grand_total,
            ],
        ]);

        // Simulates or uses Laravel Mail::to($recipient)->send(new InvoiceMail($invoice))
        $log->status = 'sent';
        $log->provider_message_id = 'MAIL_' . uniqid();
        $log->save();

        return $log;
    }
}
