<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class IndexController extends Controller
{
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
        return Inertia::render('Blogs');
    }
}
