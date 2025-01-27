<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function store(Request $request)
    {
        $message = $request->content;
        $scheduled_at = $request->send_at;
        $platforms = $request->platforms;
        
        $user = auth()->user();
        
        $user->scheduledMessages()->create([
            'user_id' => $user->id,
            'content' => $message,
            'send_at' => $scheduled_at,
            'platforms' => $platforms,
        ]);

        return redirect()->route('dashboard');
    }
}
