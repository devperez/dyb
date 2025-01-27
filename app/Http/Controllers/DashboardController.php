<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard() 
    {
        // Fetch the user's messages
        $messages = auth()->user()->scheduledMessages()->orderBy('created_at', 'desc')->paginate(5);

        return view('dashboard', compact('messages'));
    }
}
