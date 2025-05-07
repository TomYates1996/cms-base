<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SocialMediaLink;

class SocialMediaController extends Controller
{
    public function index()
    {
        $links = SocialMediaLink::all();

        return response()->json($links);
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'link' => 'required|url|max:255',
        ]);

        $socialMediaLink = SocialMediaLink::create([
            'label' => $request->input('label'),
            'icon' => $request->input('icon'),
            'link' => $request->input('link'),
        ]);

        return response()->json($socialMediaLink);
    }
}
