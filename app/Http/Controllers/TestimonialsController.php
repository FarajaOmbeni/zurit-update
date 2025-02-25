<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimonial;

class TestimonialsController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::all();
        return view('testimonials_admindash', ['testimonials' => $testimonials]);
    }

    public function addTestimonial(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'content' => 'required|string',
        ]);

        $imageName = time().'.'.$request->image->extension();  
        $request->image->move(public_path('testimonial_images'), $imageName);

        // Assuming you have a Testimonial model
        $testimonial = new Testimonial;
        $testimonial->name = $request->name;
        $testimonial->image = $imageName;
        $testimonial->content = $request->content;
        $testimonial->save();

        return redirect()->back()->with('success', ['message' => 'Testimonial added successfully!', 'duration' => 3000]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'content' => 'required|string',
        ]);

        $testimonial = Testimonial::findOrFail($id);
        $testimonial->name = $request->name;
        $testimonial->content = $request->content;

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();  
            // $request->image->move(public_path('testimonial_images'), $imageName);
            $request->image->move('/home/zuriuhqx/public_html/testimonial_images/', $imageName);
            $testimonial->image = $imageName;
        }

        $testimonial->save();

        return redirect()->back()->with('success', ['message' => 'Testimonial updated successfully!', 'duration' => 3000]);
    }

    public function destroy($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->delete();

        return redirect()->back()->with('success', ['message' => 'Testimonial deleted successfully!', 'duration' => 3000]);
    }
}
