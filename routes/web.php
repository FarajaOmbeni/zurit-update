<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NetworthController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\ZuriScoreController;
use App\Http\Controllers\CoachAdminController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\TestimonialsController;
use App\Http\Controllers\CreateMeetingController;
use App\Http\Controllers\PaymentStatusController;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\ElearningController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseMaterialController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ElearningQuizController;
use App\Http\Controllers\CertificateController;

Route::get('/', [IndexController::class, 'index'])->name('home');


Route::get('/dashboard', function () {
    return redirect()->route('msme.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
Route::post('/submit-quiz', [QuestionnaireController::class, 'submitQuestionnaire'])->name('submit.quiz');
Route::get('/money-quiz', function () {
    return Inertia::render('MoneyQuiz');
})->name('money-quiz');
Route::get('/advisory', function () {
    return Inertia::render('Advisory');
})->name('advisory');
Route::get('/business-support', function () {
    return Inertia::render('BusinessSupport');
})->name('business.support');
Route::post('/sendMessage', [IndexController::class, 'sendMessage'])->name('send.message');
Route::post('/sendFeedback', [EventsController::class, 'eventFeedback'])->name('send.feedback');
Route::post('/sendEmail', [IndexController::class, 'sendEmail'])->name('send.email');
Route::get('/calendar', [IndexController::class, 'calendar'])->name('calendar');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    /////////////////////////////////////////////////////////
    //////////////////  BUDGET ROUTES ///////////////////////
    ////////////////////////////////////////////////////////
    Route::get('/user/budget', [BudgetController::class, 'index'])->name('budget.index');
    Route::get('/user/budget/budgets', [BudgetController::class, 'budgets'])->name('budget.budgets');
    Route::post('addIncome', [BudgetController::class, 'storeIncome'])->name('income.store');
    Route::put('/income/{id}', [BudgetController::class, 'updateIncome'])->name('income.edit');
    // Edit past-month income without touching recurrence rule
    Route::put('/income/past/{id}', [BudgetController::class, 'updatePastIncome'])->name('income.past.edit');
    Route::delete('/income/{id}', [BudgetController::class, 'destroyIncome'])->name('income.destroy');
    Route::post('addExpense', [BudgetController::class, 'storeExpense'])->name('expense.store');
    Route::put('/expense/{id}', [BudgetController::class, 'updateExpense'])->name('expense.edit');
    // Edit past-month expense without touching recurrence rule
    Route::put('/expense/past/{id}', [BudgetController::class, 'updatePastExpense'])->name('expense.past.edit');
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
    Route::delete('/user/invest/{id}', [InvestmentController::class, 'destroy'])->name('invest.destroy');
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

    /////////////////////////////////////////////////////////
    //////////////////  E-LEARNING ROUTES ///////////////////////
    ////////////////////////////////////////////////////////
    Route::prefix('elearning')->group(
        function () {
            Route::get('/landing', [ElearningController::class, 'landing'])->name('elearning.landing');
            Route::get('/courses', [ElearningController::class, 'index'])->name('elearning.courses');
            Route::get('/courses/{course}', [ElearningController::class, 'show'])->name('elearning.course');
            Route::get('/quiz/{course}', [ElearningQuizController::class, 'show'])->name('elearning.quiz');
            Route::post('/quiz/{course}/submit', [ElearningQuizController::class, 'submit'])->name('elearning.quiz.submit');
            Route::get('/quiz/{course}/results', [ElearningQuizController::class, 'results'])->name('elearning.quiz.results');
            Route::get('/certificate/{course}', [CertificateController::class, 'generate'])->name('elearning.certificate');
        }
    );

    Route::get('/course-materials/{material}', [CourseMaterialController::class, 'show'])->name('course-materials.show');
    Route::get('/course-materials/{material}/viewer', [CourseMaterialController::class, 'viewer'])->name('course-materials.viewer');
    Route::get('/user/zuriscore', [ZuriScoreController::class, 'index'])->name('zuriscore.index');
    Route::post('/user/zuriscore', [ZuriScoreController::class, 'get_report'])->name('zuriscore.post');
    Route::get('/user/zuriscore/processing/{payment_id}', [ZuriScoreController::class, 'processing'])->name('zuriscore.processing');
    Route::get('/user/zuriscore/status/{payment_id}', [ZuriScoreController::class, 'checkPaymentStatus'])->name('zuriscore.status');

    /////////////////////////////////////////////////////////
    //////////////////  QUESTIONNAIRES ROUTES /////////////
    ////////////////////////////////////////////////////////
    Route::get('/user/questionnaires', [QuestionnaireController::class, 'index'])->name('questionnaires.index');
    Route::post('/questionnaires/onboarding', [QuestionnaireController::class, 'submitOnboarding'])->name('questionnaires.onboarding');
    Route::post('/questionnaires/personality', [QuestionnaireController::class, 'submitPersonality'])->name('questionnaires.personality');
    Route::post('/questionnaires/risk', [QuestionnaireController::class, 'submitRiskTolerance'])->name('questionnaires.risk');
    Route::post('/questionnaires/next-step', [QuestionnaireController::class, 'submitNextStep'])->name('questionnaires.next-step');

    // Coach routes (protected by 'coach' middleware)
    Route::middleware(['auth', 'coach'])->group(function () {
        // Coach Dashboard routes (for coaches to view their clients)
        Route::get('/coach', [CoachController::class, 'dashboard'])->name('coach.dashboard');
        Route::get('/coach/client/{clientId}', [CoachController::class, 'viewClient'])->name('coach.client.view');
        // Coach meetings
        Route::get('/coach/meetings', [CoachController::class, 'meetings'])->name('coach.meetings');
        Route::post('/coach/meetings', CreateMeetingController::class);
    });

    // User-coach assignment routes (for users, not coaches)
    Route::get('/user/coach', [CoachController::class, 'index'])->name('coach.index');
    Route::post('/user/coach/assign', [CoachController::class, 'assignCoach'])->name('coach.assign');
    Route::delete('/user/coach/remove', [CoachController::class, 'removeCoach'])->name('coach.remove');

    // Admin Coaching routes
    Route::prefix('admin/coaching')->name('coaching.')->group(function () {
        Route::get('/', [CoachAdminController::class, 'index'])->name('index');
        Route::get('/create', [CoachAdminController::class, 'create'])->name('create');
        Route::post('/', [CoachAdminController::class, 'store'])->name('store');
        Route::get('/search-users', [CoachAdminController::class, 'searchUsers'])->name('search-users');
        Route::get('/{id}', [CoachAdminController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CoachAdminController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CoachAdminController::class, 'update'])->name('update');
        Route::delete('/{id}', [CoachAdminController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/clients', [CoachAdminController::class, 'getClients'])->name('clients');
        Route::post('/{id}/assign-user', [CoachAdminController::class, 'assignUser'])->name('assign-user');
        Route::post('/{id}/remove-user', [CoachAdminController::class, 'removeUser'])->name('remove-user');
    });
});

Route::middleware(['auth', 'admin'])->group(function () {
    /////////////////////////////////////////////////////////
    //////////////////  ADMIN ROUTES ///////////////////////
    ////////////////////////////////////////////////////////
    Route::get('/admin', [AdminController::class, 'users'])->name('users.index');
    Route::get('/admin/system', [AdminController::class, 'system'])->name('system.index');

    //////////BLOGS ROOUTES//////////
    Route::get('/admin/blogs', [BlogController::class, 'index'])->name('blogs.index');
    Route::post('/admin/blogs', [BlogController::class, 'store'])->name('blogs.store');
    Route::post('/admin/blogs/edit/{id}', [BlogController::class, 'update'])->name('blogs.update');
    Route::delete('/admin/blogs/delete/{id}', [BlogController::class, 'destroy'])->name('blogs.destroy');

    ////////////EVENTS ROUTES/////////////
    Route::get('/admin/events', [EventsController::class, 'index'])->name('events.index');
    Route::post('/admin/events', [EventsController::class, 'store'])->name('events.store');
    Route::post('/admin/update/{id}', [EventsController::class, 'update'])->name('events.update');
    Route::delete('/admin/delete/{id}', [EventsController::class, 'destroy'])->name('events.destroy');


    Route::get('/admin/testimonials', [TestimonialsController::class, 'index'])->name('testimonials.index');
    Route::post('/admin/testimonials', [TestimonialsController::class, 'store'])->name('testimonials.store');
    Route::post('/admin/testimonials/edit/{id}', [TestimonialsController::class, 'update'])->name('testimonials.update');
    Route::delete('/admin/testimonials/delete/{id}', [TestimonialsController::class, 'destroy'])->name('testimonials.destroy');

    Route::get('/admin/marketing', [MarketingController::class, 'index'])->name('marketing.index');
    Route::post('/admin/marketing', [MarketingController::class, 'sendEmails'])->name('marketing.send');


    Route::get('/admin/add-users', function () {
        return Inertia::render('Admin/AddUsers');
    })->name('add-users.index');

    Route::get('/admin/videos', [VideoController::class, 'index'])->name('videos.index');
    Route::post('/admin/videos', [VideoController::class, 'store'])->name('videos.store');

    Route::prefix('admin')->group(function () {
        Route::get('/courses', [CourseController::class, 'index'])->name('admin.courses.index');
        Route::get('/courses/create-main', [CourseController::class, 'createMain'])->name('admin.courses.create-main');
        Route::get('/courses/create', [CourseController::class, 'create'])->name('admin.courses.create');
        Route::post('/courses', [CourseController::class, 'store'])->name('admin.courses.store');
        // Split edit/update routes
        Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('admin.courses.edit');
        Route::put('/courses/{course}', [CourseController::class, 'update'])->name('admin.courses.update');
        Route::get('/subcourses/{subcourse}/edit', [CourseController::class, 'editSubcourse'])->name('admin.subcourses.edit');
        Route::put('/subcourses/{subcourse}', [CourseController::class, 'updateSubcourse'])->name('admin.subcourses.update');
        Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('admin.courses.destroy');

        // Quiz routes
        Route::get('/quizzes', [QuizController::class, 'index'])->name('admin.quizzes.index');
        Route::get('/quizzes/create', [QuizController::class, 'create'])->name('admin.quizzes.create');
        Route::post('/quizzes', [QuizController::class, 'store'])->name('admin.quizzes.store');
        Route::delete('/quizzes/{quiz}', [QuizController::class, 'destroy'])->name('admin.quizzes.destroy');
    });

    // PDF viewing route with custom headers
    // Route::get('/course-materials/{material}', [CourseMaterialController::class, 'show'])
    //     ->middleware(\App\Http\Middleware\PreventPDFDownload::class)
    //     ->name('course-materials.show');


});
/////////////////////////////////////////////////////////
//////////////////  Elearning    ///////////////////////
////////////////////////////////////////////////////////



Route::get('/terms-and-conditions', function () {
    return Inertia::render('TermsAndConditions');
});
Route::get('/about', [IndexController::class, 'about'])->name('about');
Route::get('/goal-setting', [IndexController::class, 'goal_setting'])->name('goal');
Route::get('/investment-planner', [IndexController::class, 'investment'])->name('investment');
Route::get('/networth-calculator', [IndexController::class, 'networth'])->name('networth');
Route::get('/debt-manager', [IndexController::class, 'debt'])->name('debt');
Route::get('/training', [IndexController::class, 'training'])->name('training');
Route::get('/calculators', [IndexController::class, 'calculators'])->name('calculators');
Route::get('/questionnaires', [IndexController::class, 'questionnaires'])->name('questionnaires');
Route::get('/zuriscore', [IndexController::class, 'zuriscore'])->name('zuriscore');
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::post('/book/buy', [BookController::class, 'payment'])->name('buy.book');
Route::get('/book/processing/{payment_id}', [BookController::class, 'processing'])->name('book.processing');
Route::get('/book/status/{payment_id}', [BookController::class, 'checkPaymentStatus'])->name('book.status');
Route::get('/feedback', [IndexController::class, 'feedback'])->name('feedback');
Route::get('/blogs', [IndexController::class, 'blogs'])->name('blogs');
Route::get('/blog/{id}', [IndexController::class, 'blog'])->name('blog');
Route::get('/advisory', function () {
    return Inertia::render('Advisory');
})->name('advisory');
Route::get('/business-support', function () {
    return Inertia::render('BusinessSupport');
})->name('business.support');
Route::post('/zuri-callback', [ZuriScoreController::class, 'handleCallback'])
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->name('zuriscore.callback');


Route::post('/mpesa-callback', [MpesaController::class, 'handleCallback'])
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->name('mpesa-callback');
Route::post('/stk-push', [MpesaController::class, 'sendStkPush'])->name('stk.push');
Route::post('/chatpesa-callback', [MpesaController::class, 'handleCallback'])
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->name('chatpesa-callback');
require __DIR__ . '/auth.php';

// MSME Financial Management Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Main MSME Dashboard
    Route::get('/msme/dashboard', [App\Http\Controllers\MsmeController::class, 'dashboard'])->name('msme.dashboard');
    Route::get('/msme/business-setup', [App\Http\Controllers\MsmeController::class, 'businessSetup'])->name('msme.business-setup');
    Route::post('/msme/business-profile', [App\Http\Controllers\MsmeController::class, 'updateBusinessProfile'])->name('msme.business-profile.update');
    Route::get('/msme/reports', [App\Http\Controllers\MsmeController::class, 'reports'])->name('msme.reports');
    Route::post('/msme/generate-report', [App\Http\Controllers\MsmeController::class, 'generateReport'])->name('msme.generate-report');

    // Cashflow Management
    Route::prefix('msme/cashflow')->name('cashflow.')->group(function () {
        Route::get('/', [App\Http\Controllers\CashflowController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\CashflowController::class, 'store'])->name('store');
        Route::put('/{cashflowEntry}', [App\Http\Controllers\CashflowController::class, 'update'])->name('update');
        Route::delete('/{cashflowEntry}', [App\Http\Controllers\CashflowController::class, 'destroy'])->name('destroy');
        Route::get('/analytics', [App\Http\Controllers\CashflowController::class, 'analytics'])->name('analytics');
        Route::post('/bulk-import', [App\Http\Controllers\CashflowController::class, 'bulkImport'])->name('bulk-import');
    });

    // Pricing Models
    Route::prefix('msme/pricing')->name('pricing.')->group(function () {
        Route::get('/', [App\Http\Controllers\PricingModelController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\PricingModelController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\PricingModelController::class, 'store'])->name('store');
        Route::get('/{pricingModel}', [App\Http\Controllers\PricingModelController::class, 'show'])->name('show');
        Route::get('/{pricingModel}/edit', [App\Http\Controllers\PricingModelController::class, 'edit'])->name('edit');
        Route::put('/{pricingModel}', [App\Http\Controllers\PricingModelController::class, 'update'])->name('update');
        Route::delete('/{pricingModel}', [App\Http\Controllers\PricingModelController::class, 'destroy'])->name('destroy');
        Route::post('/calculator', [App\Http\Controllers\PricingModelController::class, 'calculator'])->name('calculator');
        Route::post('/apply-template', [App\Http\Controllers\PricingModelController::class, 'applyTemplate'])->name('apply-template');
        Route::get('/{pricingModel}/export', [App\Http\Controllers\PricingModelController::class, 'exportAnalysis'])->name('export');
        Route::post('/compare', [App\Http\Controllers\PricingModelController::class, 'compare'])->name('compare');
    });

    // Profit & Loss
    Route::prefix('msme/profit-loss')->name('profit-loss.')->group(function () {
        Route::get('/', [App\Http\Controllers\ProfitLossController::class, 'index'])->name('index');
        Route::post('/generate', [App\Http\Controllers\ProfitLossController::class, 'generate'])->name('generate');
        Route::get('/{profitLossRecord}', [App\Http\Controllers\ProfitLossController::class, 'show'])->name('show');
        Route::put('/{profitLossRecord}', [App\Http\Controllers\ProfitLossController::class, 'update'])->name('update');
        Route::delete('/{profitLossRecord}', [App\Http\Controllers\ProfitLossController::class, 'destroy'])->name('destroy');
    });

    // Balance Sheet
    Route::prefix('msme/balance-sheet')->name('balance-sheet.')->group(function () {
        Route::get('/', [App\Http\Controllers\BalanceSheetController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\BalanceSheetController::class, 'store'])->name('store');
        Route::get('/{balanceSheetRecord}', [App\Http\Controllers\BalanceSheetController::class, 'show'])->name('show');
        Route::put('/{balanceSheetRecord}', [App\Http\Controllers\BalanceSheetController::class, 'update'])->name('update');
        Route::delete('/{balanceSheetRecord}', [App\Http\Controllers\BalanceSheetController::class, 'destroy'])->name('destroy');
    });
});
