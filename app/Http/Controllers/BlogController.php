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
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $absolutePath = '/home/zuriuhqx/public_html/storage/blogs';
            $image->move($absolutePath, $imageName);
            $imagePath = '/storage/blogs/'.$imageName;
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

    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        //validate the inputs
        $request->validate([
            'blog_tag' => 'required',
            'blog_title' => 'required',
            'blog_image' => 'required|image',
            'content' => 'required',
        ]);

        $imagePath = null;

        if ($request->hasFile('blog_image')) {
            $image = $request->file('blog_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $absolutePath = '/home/zuriuhqx/public_html/storage/blogs';
            $image->move($absolutePath, $imageName);
            $imagePath = '/storage/blogs/' . $imageName;
        }

        $blog->blog_image = basename($imagePath);
        $blog->blog_tag = $request->blog_tag;
        $blog->blog_title = $request->blog_title;
        $blog->blog_message = $request->content;

        $blog->save();

        return to_route('blogs.index');
    }

    public function destroy($id)
    {
        try {
            $blog = Blog::findOrFail($id);
            $blog->delete();
            
            return to_route('blogs.index');
        } catch (\Exception $e) {
            return redirect(route('blogs.index'))->with('error', [
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
