<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Blog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::all();
        return Inertia::render('Admin/Blogs',[
            'blogs' => $blogs
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'blog_title' => 'required|string|max:255',
            'blog_tag' => 'required|string|max:255',
            'blog_image' => 'required|image',
            'content' => 'required',
        ]);

        $imagePath = null;

        if($request->hasFile('blog_image')) {
            $image = $request->file('blog_image');
            $imagePath = $image->store('blogs', 'public');
        }

        $blog = new Blog();
        $blog->blog_title = $request->blog_title;
        $blog->blog_tag = $request->blog_tag;
        $blog->blog_image = basename($imagePath);
        $blog->blog_message = $request->content;

        $blog->save();

        return to_route('blogs.index');
    }

    public function show($id)
    {
        return view('blogs');
    }


    public function edit($id)
    {
        $blog = Blog::find($id);

        return view('blogs_editdash', ['blog' => $blog]);
    }
    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $imageName = ''; // Initialize image name variable

        // Handle file upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            // $image->move(public_path('blogs_res/img'), $imageName);
            $image->move('/home/zuriuhqx/public_html/blogs_res/img', $imageName);
        }

        //validate the inputs
        $request->validate([
            'blog_image' => 'required|image',
            'blog_tag' => 'required',
            'blog_title' => 'required',
            'content' => 'required',
        ]);

        $blog->blog_image = $request->file('blog_image')->store('public/blogs');
        $blog->blog_tag = $request->blog_tag;
        $blog->blog_title = $request->blog_title;
        $blog->blog_message = $request->content;

        $blog->save();

        return redirect('/blogs_admindash')->with('success', [
            'message' => 'Blog Updated Successfully!',
            'duration' => 3000,
        ]);
    }

    public function destroy($id)
    {
        try {
            $blog = Blog::findOrFail($id);
            $blog->delete();
            return redirect('/blogs_admindash')->with('success', [
                'message' => 'Blog Deleted Successfully!',
                'duration' => 3000,
            ]);
        } catch (\Exception $e) {
            return redirect('/blogs_admindash')->with('error', [
                'message' => 'Error Deleting Blog!',
                'duration' => 3000,
            ]);
        }
    }

    public function view($slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();
        return view('blog_view', compact('blog'));
    }
}
