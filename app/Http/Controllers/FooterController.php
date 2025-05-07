<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Footer;


class FooterController extends Controller
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

        $footer = $data['item'];
        if ($footer['is_saved']) {
            $templateId = (Footer::max('template_id') ?? 0) + 1;
            $footer['template_id'] = $templateId;
            $footer['is_saved'] = true;

            $footer = Footer::find()->where('id' , $data['item']['id']);

            return response()->json([
                'message' => 'Template Footer saved.',
                'template_id' => $templateId,
            ]);
        }
        return response()->json([
            'message' => 'Footer not marked as saved.',
        ], 400);
    }

    public function save_footer(Request $request, Footer $footer)
    {
        if ($request->is_saved) {
            $templateId = (Footer::max('template_id') ?? 0) + 1;
    
            $footer->update([
                'is_saved' => $request->is_saved,
                'name' => $request->name,
                'template_id' => $templateId,
            ]);
    
        } else {
            $footer->update([
                'is_saved' => false,
                'name' => null,
                'template_id' => null,
            ]);
        }
    
        return response()->json(['message' => 'Footer saved successfully.']);
    }
}
