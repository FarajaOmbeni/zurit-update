<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimonial;
use Inertia\Inertia;

class TestimonialsController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::all();
        return Inertia::render('Admin/Testimonials', ['testimonials' => $testimonials]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $absolutePath = '/home/zuriuhqx/public_html/storage/events';
            $image->move($absolutePath, $imageName);
            $imagePath = '/storage/events/' . $imageName;
        }

        // Assuming you have a Testimonial model
        $testimonial = new Testimonial;
        $testimonial->name = $request->name;
        $testimonial->image = basename($imagePath);
        $testimonial->content = $request->content;
        $testimonial->save();

        return to_route('testimonials.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $testimonial = Testimonial::findOrFail($id);
        $testimonial->name = $request->name;
        $testimonial->content = $request->content;

        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $absolutePath = '/home/zuriuhqx/public_html/storage/events';
            $image->move($absolutePath, $imageName);
            $imagePath = '/storage/events/' . $imageName;
        }

        $testimonial->image = basename($imagePath);
        $testimonial->update();

        return to_route('testimonials.index');
    }

    public function destroy($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->delete();

        return to_route('testimonials.index');
    }
}
