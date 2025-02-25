<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Blog;
use App\Models\Book;
use Illuminate\Support\Carbon;

use Illuminate\Http\Request;

class InsightsController extends Controller
{
    public function insightdata()
    {
        // Fetch data from the database
        $totalUsers = User::count();
        $admins = User::where('role', 2)->count();
        $editors = User::where('role', 1)->count();
        $regularUsers = User::where('role', 0)->count();
        
        // Fetch counts for total books and blogs
        $totalBooks = Book::count();
        $totalBlogs = Blog::count();

        // Fetch user growth data based on created_at column
        $userGrowthData = $this->getUserGrowthData();

        // Fetch recent activities
        $recentUsers = User::latest()->take(5)->get();
        $recentBlogs = Blog::with('user')->latest()->take(5)->get();
        $recentBooks = Book::latest()->take(5)->get();

        // Pass data to the view
        return view('insights_admindash', [
            'totalUsers' => $totalUsers,
            'admins' => $admins,
            'editors' => $editors,
            'regularUsers' => $regularUsers,
            'userGrowthData' => $userGrowthData,
            'totalBooks' => $totalBooks,
            'totalBlogs' => $totalBlogs,
            'recentUsers' => $recentUsers,
            'recentBlogs' => $recentBlogs,
            'recentBooks' => $recentBooks,
        ]);
    }

    // Function to get user growth data
    private function getUserGrowthData()
    {
        $labels = [];
        $data = [];

        // Fetch user growth data based on created_at column
        $userGrowth = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        foreach ($userGrowth as $entry) {
            $labels[] = Carbon::parse($entry->date)->format('M d'); // Format date as needed
            $data[] = $entry->count;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
