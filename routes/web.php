<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\GoalController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\NetworthController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/user/budget', [BudgetController::class, 'index'])->name('user.budget');
    Route::get('/user/debt', [DebtController::class, 'index'])->name('user.debt');
    Route::get('/user/goal', [GoalController::class, 'index'])->name('user.goal');
    Route::get('/user/invest', [InvestmentController::class, 'index'])->name('user.invest');
    Route::get('/user/networth', [NetworthController::class, 'index'])->name('user.networth');
    Route::get('/user/calculators', function () {
        return Inertia::render('UserDashboard/Calculators');
    })->name('user.calculators');
});

Route::get('/about', [IndexController::class, 'about'])->name('about');
Route::get('/goal-setting', [IndexController::class, 'goal_setting'])->name('goal');
Route::get('/investment-planner', [IndexController::class, 'investment'])->name('investment');
Route::get('/networth-calculator', [IndexController::class, 'networth'])->name('networth');
Route::get('/debt-manager', [IndexController::class, 'debt'])->name('debt');
Route::get('/budget-planner', [IndexController::class, 'budget'])->name('budget');
Route::get('/training', [IndexController::class, 'training'])->name('training');
Route::get('/books', [IndexController::class, 'books'])->name('books');
Route::get('/feedback', [IndexController::class, 'feedback'])->name('feedback');
Route::get('/blogs', [IndexController::class, 'blogs'])->name('blogs');

require __DIR__.'/auth.php';
