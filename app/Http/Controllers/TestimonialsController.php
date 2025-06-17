<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

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
            $imagePath = $image->move(storage_path('app/public/testimonials'), $imageName);
        }
        try {
            // Assuming you have a Testimonial model
            $testimonial = new Testimonial;
            $testimonial->name = $request->name;
            $testimonial->image = basename($imagePath);
            $testimonial->content = $request->content;
            $testimonial->save();

            return to_route('testimonials.index');
        } catch (Exception $e) {
            return to_route('testimonials.index')->withErrors("Failed to add testimonial");
            Log::info($e);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            // 'image'   => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            $testimonial = Testimonial::findOrFail($id);
            $testimonial->name = $request->name;
            $testimonial->content = $request->content;

            $imagePath = null;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->move(storage_path('app/public/testimonials'), $imageName);
            }

            $testimonial->image = basename($imagePath);
            $testimonial->update();

            return to_route('testimonials.index');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return to_route('testimonials.index')->withErrors('Failed to edit testimonial');
            Log::info($e);
        }
    }

    public function destroy($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->delete();

        return to_route('testimonials.index');
    }
}
