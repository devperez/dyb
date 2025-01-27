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
        }
    }

    private function sendToSlack(ScheduledMessage $message): void
    {
        // Message content
        $payload = [
            'text' => $message->content,
        ];

        // Send the message to Slack
        $response = Http::post(env('SLACK_WEBHOOK_URL'), $payload);
        
        // Vérifiez si l'envoi a réussi
        if ($response->successful()) {
            // Message envoyé avec succès
            echo 'Message envoyé à Slack avec succès !';
            // Mark the message as sent
            $message->update(['sent_at' => now()]);
            $message->status = 'sent';
            $message->save();
        } else {
            // Erreur lors de l'envoi
            echo 'Erreur lors de l\'envoi du message à Slack.';
        }
    }
}
