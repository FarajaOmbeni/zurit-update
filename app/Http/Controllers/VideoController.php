<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(){        
        return view('video_admindash');
    }

    public function store(Request $request)
    {
        $video = new Video;

        $fullLink = $request->input('video_link');

        // Find position of first '=' and '&'
        $startPos = strpos($fullLink, '=');
        $endPos = strpos($fullLink, '&');

        if ($startPos !== false) {
            $startPos++; // Move past the '='
            if ($endPos === false) {
                // If there's no '&', take everything after '='
                $videoId = substr($fullLink, $startPos);
            } else {
                // If there's a '&', take the substring between '=' and '&'
                $length = $endPos - $startPos;
                $videoId = substr($fullLink, $startPos, $length);
            }
            $video->video_link = $videoId;
        } else {
            // Handle the case where there's no '=' in the link
            $video->video_link = $fullLink;
        }

        $video->save();

        return redirect()->back()->with('success', [
            'message' => 'Video Added Successfully!',
            'duration' => 3000,
        ]);
    }
}
