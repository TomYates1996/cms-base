<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Header;


class HeaderController extends Controller
{
    public function create_save(Request $request)
    {
        $data = $request->validate([
            'item' => 'required|array',
            'item.is_saved' => 'required|boolean',
            'item.name' => 'nullable|string', 
            'item.logo' => 'nullable|array', 
        ]);
        dd($data['item']);

        $header = $data['item'];
        if ($header['is_saved']) {
            $templateId = (Header::max('template_id') ?? 0) + 1;
            $header['template_id'] = $templateId;
            $header['is_saved'] = true;

            $header = Header::find()->where('id' , $data['item']['id']);

            return response()->json([
                'message' => 'Template Header saved.',
                'template_id' => $templateId,
            ]);
        }
        return response()->json([
            'message' => 'Header not marked as saved.',
        ], 400);
    }

    public function save_header(Request $request, Header $header)
    {
        if ($request->is_saved) {
            $templateId = (Header::max('template_id') ?? 0) + 1;
    
            $header->update([
                'is_saved' => $request->is_saved,
                'name' => $request->name,
                'template_id' => $templateId,
            ]);
    
        } else {
            $header->update([
                'is_saved' => false,
                'name' => null,
                'template_id' => null,
            ]);
        }
    
        return response()->json(['message' => 'Header saved successfully.']);
    }
}
