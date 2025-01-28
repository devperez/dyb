<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserPlatformController extends Controller
{
    public function index()
    {
        $userPlatforms = Auth::user()->platforms; // Relation avec la table `user_platforms`
        return view('configuration', compact('userPlatforms'));
    }

    public function update(Request $request)
{
    $validated = $request->validate([
        'slack_webhook' => 'nullable|url',
        'telegram_bot_token' => 'nullable|string|max:255',
        'telegram_chat_id' => 'nullable|string|max:255',
    ]);

    $user = Auth::user();

    // Slack configuration
    if (isset($validated['slack_webhook'])) {
        $slackPlatform = $user->platforms()->where('platform', 'slack')->first();
        if ($slackPlatform && $slackPlatform->config['webhook'] !== $validated['slack_webhook']) {
            $slackPlatform->update([
                'config' => ['webhook' => $validated['slack_webhook']],
            ]);
        } elseif (!$slackPlatform) {
            $user->platforms()->create([
                'platform' => 'slack',
                'config' => ['webhook' => $validated['slack_webhook']],
            ]);
        }
    }

    // Telegram configuration
    if (isset($validated['telegram_bot_token']) || isset($validated['telegram_channel_id'])) {
        $telegramPlatform = $user->platforms()->where('platform', 'telegram')->first();
        if ($telegramPlatform) {
            $config = $telegramPlatform->config;
            $updated = false;

            if (isset($validated['telegram_bot_token']) && $config['bot_token'] !== $validated['telegram_bot_token']) {
                $config['bot_token'] = $validated['telegram_bot_token'];
                $updated = true;
            }

            if (isset($validated['telegram_channel_id']) && $config['chat_id'] !== $validated['telegram_channel_id']) {
                $config['chat_id'] = $validated['telegram_chat_id'];
                $updated = true;
            }

            if ($updated) {
                $telegramPlatform->update([
                    'config' => $config,
                ]);
            }
        } else {
            $user->platforms()->create([
                'platform' => 'telegram',
                'config' => [
                    'bot_token' => $validated['telegram_bot_token'] ?? null,
                    'chat_id' => $validated['telegram_chat_id'] ?? null,
                ],
            ]);
        }
    }

    return redirect()->route('configuration')->with('success', 'Configuration mise à jour avec succès.');
}


    // public function update(Request $request)
    // {
    //     $validated = $request->validate([
    //         'slack_webhook' => 'nullable|url',
    //         'telegram_bot_token' => 'nullable|string|max:255',
    //         'telegram_channel_id' => 'nullable|string|max:255',
    //     ]);

    //     $user = Auth::user();

    //     // Slack configuration
    //     $user->platforms()->updateOrCreate(
    //         ['platform' => 'slack'],
    //         ['config' => ['webhook' => $validated['slack_webhook']]]
    //     );

    //     // Telegram configuration
    //     $user->platforms()->updateOrCreate(
    //         ['platform' => 'telegram'],
    //         ['config' => [
    //             'bot_token' => $validated['telegram_bot_token'],
    //             'chat_id' => $validated['telegram_channel_id'],
    //         ]]
    //     );

    //     return redirect()->route('configuration')->with('success', 'Configuration mise à jour avec succès.');
    // }
}
