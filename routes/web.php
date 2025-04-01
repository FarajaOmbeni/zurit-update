<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BlogController;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NetworthController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\TestimonialsController;
use App\Http\Controllers\VideoController;

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
    /////////////////////////////////////////////////////////
    //////////////////  BUDGET ROUTES ///////////////////////
    ////////////////////////////////////////////////////////
    Route::get('/user/budget', [BudgetController::class, 'index'])->name('budget.index');
    Route::post('addIncome', [BudgetController::class, 'storeIncome'])->name('income.store');
    Route::put('/income/{id}', [BudgetController::class, 'updateIncome'])->name('income.edit');
    Route::delete('/income/{id}', [BudgetController::class, 'destroyIncome'])->name('income.destroy');
    Route::post('addExpense', [BudgetController::class, 'storeExpense'])->name('expense.store');
    Route::put('/expense/{id}', [BudgetController::class, 'updateExpense'])->name('expense.edit');
    Route::delete('/expense/{id}', [BudgetController::class, 'destroyExpense'])->name('expense.destroy');

    /////////////////////////////////////////////////////////
    //////////////////  DEBT ROUTES ///////////////////////
    ////////////////////////////////////////////////////////
    Route::get('/user/debt', [DebtController::class, 'index'])->name('debt.index');
    Route::post('/user/debt', [DebtController::class, 'store'])->name('debt.store');
    Route::post('/user/debt/{id}', [DebtController::class, 'destroy'])->name('debt.destroy');
    Route::put('/user/debt/{id}', [DebtController::class, 'update'])->name('debt.update');
    Route::put('/user/debt/contribute/{id}', [DebtController::class, 'contribute'])->name('debt.contribute');

    /////////////////////////////////////////////////////////
    //////////////////  GOAL ROUTES ///////////////////////
    ////////////////////////////////////////////////////////
    Route::get('/user/goal', [GoalController::class, 'index'])->name('goal.index');
    Route::post('/user/goal', [GoalController::class, 'store'])->name('goal.store');
    Route::post('/user/goal/{id}', [GoalController::class, 'destroy'])->name('goal.destroy');
    Route::put('/user/goal/{id}', [GoalController::class, 'update'])->name('goal.update');
    Route::put('/user/goal/contribute/{id}', [GoalController::class, 'contribute'])->name('goal.contribute');
    /////////////////////////////////////////////////////////
    //////////////////  INVESTMENT ROUTES ///////////////////////
    ////////////////////////////////////////////////////////
    Route::get('/user/invest', [InvestmentController::class, 'index'])->name('invest.index');
    Route::post('/user/invest', [InvestmentController::class, 'store'])->name('invest.store');
    Route::post('/user/invest/{id}', [InvestmentController::class, 'destroy'])->name('invest.destroy');
    Route::put('/user/invest/{id}', [InvestmentController::class, 'update'])->name('invest.update');
    Route::put('/user/invest/contribute/{id}', [InvestmentController::class, 'contribute'])->name('invest.contribute');

    /////////////////////////////////////////////////////////
    //////////////////  NETWORTH ROUTES ///////////////////////
    ////////////////////////////////////////////////////////
    Route::get('/user/networth', [NetworthController::class, 'index'])->name('networth.index');
    Route::post('/user/asset', [NetworthController::class, 'storeAsset'])->name('asset.store');
    Route::post('/user/asset/{id}', [NetworthController::class, 'destroyAsset'])->name('asset.destroy');
    Route::put('/user/asset/{id}', [NetworthController::class, 'updateAsset'])->name('asset.update');

    Route::post('/user/liability', [NetworthController::class, 'storeLiability'])->name('liability.store');
    Route::post('/user/liability/{id}', [NetworthController::class, 'destroyLiability'])->name('liability.destroy');
    Route::put('/user/liability/{id}', [NetworthController::class, 'updateLiability'])->name('liability.update');

    /////////////////////////////////////////////////////////
    //////////////////  OTHER ROUTES ///////////////////////
    ////////////////////////////////////////////////////////
    Route::get('/user/calculators', function () {
        return Inertia::render('UserDashboard/Calculators');
    })->name('calculator.index');
});

/////////////////////////////////////////////////////////
//////////////////  ADMIN ROUTES ///////////////////////
////////////////////////////////////////////////////////
Route::get('/admin/users', [AdminController::class, 'users'])->name('users.index');
Route::get('/admin/system', [AdminController::class, 'system'])->name('system.index');

//////////BLOGS ROOUTES//////////
Route::get('/admin/blogs', [BlogController::class, 'index'])->name('blogs.index');
Route::post('/admin/blogs', [BlogController::class, 'store'])->name('blogs.store');

////////////EVENTS ROUTES/////////////
Route::get('/admin/events', [EventsController::class, 'index'])->name('events.index');
Route::post('/admin/events', [EventsController::class, 'store'])->name('events.store');


Route::get('/admin/testimonials', [TestimonialsController::class, 'index'])->name('testimonials.index');
Route::post('/admin/testimonials', [TestimonialsController::class, 'store'])->name('testimonials.store');

Route::get('/admin/marketing', [MarketingController::class, 'index'])->name('marketing.index');
Route::post('/admin/marketing', [MarketingController::class, 'sendEmails'])->name('marketing.send');


Route::get('/admin/add-users', function () {
    return Inertia::render('Admin/AddUsers');
})->name('add-users.index');

Route::get('/admin/videos', [VideoController::class, 'index'])->name('videos.index');
Route::post('/admin/videos', [VideoController::class, 'store'])->name('videos.store');

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
Route::get('/blog/{id}', [IndexController::class, 'blog'])->name('blog');
Route::get('/advisory', function () {
    return Inertia::render('Advisory');
})->name('advisory');
Route::get('/business-support', function() {
    return Inertia::render('BusinessSupport');
})->name('business.support');

require __DIR__.'/auth.php';
