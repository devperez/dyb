<?php

namespace App\Jobs;

use App\Models\ScheduledMessage;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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

        $user_id = $message->user_id;
        $user = User::find($user_id);
        
        // Fetch the user's Slack configuration
        $slackPlatform = $user->platforms()->where('platform', 'slack')->first();

        if ($slackPlatform && isset($slackPlatform->config['webhook'])) {
            $slackWebhookUrl = $slackPlatform->config['webhook'];

            // Send the message to Slack
            $response = Http::post($slackWebhookUrl, $payload);

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
    }

    public function sendToTelegram(ScheduledMessage $message)
    {
        // Fetch the user's Telegram configuration
        $telegramPlatform = $message->user->platforms()->where('platform', 'telegram')->first();

        // Check if the user has a configuration for Telegram
        if ($telegramPlatform && isset($telegramPlatform->config['bot_token']) && isset($telegramPlatform->config['chat_id'])) {
            // Telegram API URL
            $apiUrl = "https://api.telegram.org/bot" . $telegramPlatform->config['bot_token'] . "/sendMessage";

            // Message data
            $payload = [
                'chat_id' => $telegramPlatform->config['chat_id'], // Chat ID
                'text' => $message->content, // Message content
            ];

            // Send the request
            $response = Http::post($apiUrl, $payload);

            // Check if sending went through
            if ($response->successful()) {
                Log::info('Message envoyé avec succès à Telegram.');

                // Set the message as sent
                $message->update(['sent_at' => now()]);
                $message->status = 'sent';
                $message->save();
            } else {
                Log::error('Erreur lors de l\'envoi du message à Telegram : ' . $response->body());
            }
        } else {
            Log::error('Aucune configuration Telegram trouvée pour cet utilisateur.');
        }
    }
}
