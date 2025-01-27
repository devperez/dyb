<?php

namespace App\Jobs;

use App\Models\ScheduledMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get the unsent messages
        $messages = ScheduledMessage::where('send_at', '<=', now())
            ->where('status', 'pending')
            ->get();
        Log::info('Messages à envoyer : ' . $messages->count());
        Log::info($messages);
        foreach ($messages as $message) {
            // Check the platform
            if (in_array('slack', $message->platforms)) {
                // Send the message to Slack
                Log::info('Envoi du message à Slack...');
                $this->sendToSlack($message);
            }
            if (in_array('telegram', $message->platforms)) {
                // Send the message to Telegram
                Log::info('Envoi du message à Telegram...');
                $this->sendToTelegram($message);
            }
        }
    }

    public function sendToSlack(ScheduledMessage $message): void
    {
        // Message content
        $payload = [
            'text' => $message->content,
        ];

        // Send the message to Slack
        $response = Http::post(env('SLACK_WEBHOOK_URL'), $payload);

        // Check if sending went through
        if ($response->successful()) {
            Log::info('Message envoyé avec succès à Slack.');
            // Set the message as sent
            $message->update(['sent_at' => now()]);
            $message->status = 'sent';
            $message->save();
        } else {
            // error
            echo 'Erreur lors de l\'envoi du message à Slack.';
        }
    }

    public function sendToTelegram(ScheduledMessage $message)
    {
        // Telegram API URL
        $apiUrl = "https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage";

        // Message data
        $payload = [
            'chat_id' => env('TELEGRAM_CHANNEL_ID'), // Channel ID
            'text' => $message->content, // Message
        ];

        // Send request to Telegram
        $response = Http::post($apiUrl, $payload);

        // Check if sending went though
        if ($response->successful()) {
            Log::info('Message envoyé avec succès à Telegram.');

            // Set message as sent
            $message->update(['sent_at' => now()]);
            $message->status = 'sent';
            $message->save();
        } else {
            Log::error('Erreur lors de l\'envoi du message à Telegram : ' . $response->body());
        }
    }
}
