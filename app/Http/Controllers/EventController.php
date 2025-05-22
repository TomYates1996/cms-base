<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;


class EventController extends Controller
{
    // Load Event Home
    public function load_events()
    {
        $events = Event::all();

        return Inertia::render('crm/events/EventsHome', [
            'events' => $events,
        ]);
    }
}
