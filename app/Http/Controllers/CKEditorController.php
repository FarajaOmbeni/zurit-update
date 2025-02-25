<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CKEditorController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $newName = $fileName . '_' . now()->timestamp . '.' . $extension;

            // Use $newName here instead of $fileName
            $request->file('upload')->move('/home/zuriuhqx/public_html/media', $newName);
            
            
            
            

            $url = 'https://zuritconsulting.com/media/' . $newName;
            return response()->json(['fileName' => $newName, 'uploaded' => 1, 'url' => $url]);
        }
    }
}
