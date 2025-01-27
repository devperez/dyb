<?php

namespace App\Http\Controllers;

use App\Jobs\SendMessageJob;
use Illuminate\Http\Request;
use App\Models\ScheduledMessage;

class ScheduleController extends Controller
{
    public function store(Request $request)
    {
        $message = $request->content;
        $scheduled_at = $request->send_at;
        $platforms = $request->platforms;
        
        $user = auth()->user();
        
        
        $action = $request->input('action');
        if ($action === 'store') {
            $newMessage = $user->scheduledMessages()->create([
                'user_id' => $user->id,
                'content' => $message,
                'send_at' => $scheduled_at,
                'platforms' => $platforms,
            ]);
            session()->flash('status', 'Votre message a été programmé avec succès !');
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
            session()->flash('status', 'Votre message a été publié avec succès !');
        }
        return redirect()->route('dashboard')->with('refresh', true);
    }

    public function update(Request $request, ScheduledMessage $scheduledMessage)
    {
        $scheduledMessage->update([
            'send_at' => $request->send_at,
        ]);

        return redirect()->route('dashboard')->with('refresh', true);
    }
}
