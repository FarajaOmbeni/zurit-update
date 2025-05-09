<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Inertia\Inertia;
use App\Models\Event;
use App\Models\Video;
use App\Models\PastEvent;
use App\Models\Testimonial;
use App\Http\Controllers\Controller;

use function Termwind\render;

class IndexController extends Controller
{
    public function index()
    {
        // $events = Event::orderby('date', 'asc')->paginate(3);
        $events = Event::all();
        $pastevents = PastEvent::orderby('date', 'desc')->paginate(4);
        $testimonials = Testimonial::all();
        foreach ($pastevents as $pastevent) {
            $date = \DateTime::createFromFormat('Y-m-d', $pastevent->date);
            $pastevent->date = $date->format('F j, Y');
        }
        $latestVideo = Video::orderBy('created_at', 'desc')->first();

        return Inertia::render('Welcome', [
            'events' => $events,
            'pastevents' => $pastevents,
            'testimonials' => $testimonials,
            'video' => $latestVideo,
        ]);
    }

    public function about()
    {
        return Inertia::render('About');
    }
    public function goal_setting()
    {
        return Inertia::render('GoalSetting');
    }
    public function budget()
    {
        return Inertia::render('BudgetPlanner');
    }
    public function debt()
    {
        return Inertia::render('DebtManager');
    }
    public function networth()
    {
        return Inertia::render('NetworthCalculator');
    }
    public function investment()
    {
        return Inertia::render('InvestmentPlanner');
    }
    public function training()
    {
        return Inertia::render('Training');
    }
    public function books()
    {
        return Inertia::render('Books');
    }
    public function feedback()
    {
        return Inertia::render('Feedback');
    }

    public function blogs()
    {
        $blogs = Blog::all();
        return Inertia::render('Blogs', [
            'blogs' => $blogs
        ]);
    }

    public function blog($id)
    {
        $blog = Blog::find($id);
        $blogs = Blog::all();
        return Inertia::render('Blog', [
            'blog' => $blog,
            'blogs' => $blogs
        ]);
    }
}
