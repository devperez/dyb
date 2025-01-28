<?php

namespace App\Http\Controllers;

use App\Jobs\SendMessageJob;
use Illuminate\Http\Request;
use App\Models\ScheduledMessage;

class ScheduleController extends Controller
{
    public function store(Request $request)
    {
        //$platforms = $request->platforms;
        $validated = $request->validate([
            'content' => 'required|string|min:5|max:500',
            'platforms' => 'required|array|min:1',         
        ]);

        $message = $validated['content'];
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); // Get rid of special characters
        
        $platforms = $validated['platforms'];
        $scheduled_at = $request->send_at;
        $user = auth()->user();

        if($message && $platforms) {
            $action = $request->input('action');
            if ($action === 'store') {
                $newMessage = $user->scheduledMessages()->create([
                'user_id' => $user->id,
                'content' => $message,
                'send_at' => $scheduled_at,
                'platforms' => $platforms,
                ]);
            } elseif ($action === 'post') {
                $newMessage = $user->scheduledMessages()->create([
                    'user_id' => $user->id,
                    'content' => $message,
                    'send_at' => now(),
                    'status' => 'sent',
                    'platforms' => $platforms,
                ]);

                $job = new sendMessageJob();
                if (in_array('slack', $platforms)) {
                    $job->sendToSlack($newMessage);
                }
                if (in_array('telegram', $platforms)) {
                    $job->sendToTelegram($newMessage);
                }
            }
            return redirect()->route('dashboard')->with('refresh', true);
        } else {
            return redirect()->route('dashboard')->withErrors($validator)->withInput();

            //return redirect()->route('dashboard')->with('error', 'Veuillez remplir le champ Message et choisir au moins un réseau social.');
        }
    }

    public function update(Request $request, ScheduledMessage $scheduledMessage)
    {
        $scheduledMessage->update([
            'send_at' => $request->send_at,
        ]);

        return redirect()->route('dashboard')->with('refresh', true);
    }
}
