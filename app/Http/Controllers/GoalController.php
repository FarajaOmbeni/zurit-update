<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Debt;
use App\Models\Goal;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Traits\NetIncomeCalculator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Inertia\Inertia;

class GoalController extends Controller
{
    use NetIncomeCalculator;

    public function index()
    {
        $goals = Goal::where('user_id', auth()->id())->get();
        $this->classifyGoals($goals);

        $totalGoals = $goals->count();
        $totalAmount = $goals->filter(function ($goal) {
            return $goal->current_amount < $goal->goal_amount;
        })->sum('goal_amount');
        $totalCompleted = $goals->filter(function ($goal) {
            return $goal->current_amount == $goal->goal_amount;
        })->sum('goal_amount');

        $totalBalance = $goals->filter(function ($goal) {
            return $goal->current_amount < $goal->goal_amount;
        })->sum(function ($goal) {
            return $goal->goal_amount - $goal->current_amount;
        });

        $completedGoals = $goals->filter(function ($goal) {
            return $goal->current_amount >= $goal->goal_amount;
        })->count();

        $completionPercentages = $goals->map(function ($goal) {
            return ($goal->current_amount / $goal->goal_amount) * 100;
        });

        $netIncome = $this->calculateNetIncome(auth()->id());

        // Format goals data with titles and creation dates
        $goalsData = $goals->map(function ($goal) {
            return [
                'title' => $goal->title,
                'completion' => ($goal->current_amount / $goal->goal_amount) * 100,
                'created_at' => Carbon::parse($goal->created_at)->format('M Y')
            ];
        })->sortByDesc('created_at')->values();

        return Inertia::render('UserDashboard/GoalSetting', [
            'goals' => $goals,
            'netIncome' => $netIncome,
            'totalGoals' => $totalGoals,
            'completedGoals' => $completedGoals,
            'goalsData' => $goalsData,
            'totalAmount' => $totalAmount,
            'totalCompleted' => $totalCompleted,
            'totalBalance' => $totalBalance
        ]);
    }



    public function storeGoal(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'goal_amount' => 'required|numeric',
            'current_amount' => 'nullable|numeric',
            'description' => 'nullable|string',
            'otherDescription' => 'required_if:description,other|string|nullable',
            'deadline' => 'required|date',
        ]);

        $validatedData['user_id'] = Auth::id();

        // Handle the 'other' category
        if ($validatedData['description'] == 'other') {
            $validatedData['description'] = $validatedData['otherDescription'];
        }

        $expense = new Expense;
        $expense->user_id = Auth::id();
        $expense->expense_type = $request->input('title');
        $expense->actual_expense = $request->input('current_amount');
        $expense->is_goal = 1;
        $expense->save();

        Goal::create($validatedData);

        return redirect('user_goalsetting')->with('success', [
            'message' => 'Goal Added Succesfully',
            'duration' => 3000,
        ]);
    }

    public function addcurrentamount(Request $request, $id)
    {
        $goal = Goal::find($id);
        $addedAmount = $request->input('addedAmount');
        $goal->current_amount += $addedAmount;
        $goal->save();

        // Find the expense that matches the debt name for the current user
        $expense = Expense::where('expense_type', $goal->title)
            ->where('user_id', auth()->id())
            ->first();

        // Check if the expense exists before updating
        if ($expense) {
            $expense->actual_expense += $request->input('addedAmount');
            $expense->save();
        } else {
            // If the expense doesn't exist, create a new one
            Expense::create([
                'user_id' => auth()->id(),
                'expense_type' => $goal->title,
                'actual_expense' => $request->input('addedAmount'),
                'is_goal' => 1,
                // Add any other necessary fields
            ]);
        }
        // Compare expenses with goals and delete if they match
        $goalExpenseComparison = Expense::where('expenses.user_id', auth()->id())
            ->join('goals', function ($join) {
                $join->on('expenses.expense_type', '=', 'goals.title')
                    ->where(
                        'goals.user_id',
                        '=',
                        auth()->id()
                    );
            })
            ->select('expenses.id', 'expenses.actual_expense', 'goals.goal_amount', 'goals.title')
            ->get();

        foreach ($goalExpenseComparison as $comparison) {
            if ($comparison->actual_expense == $comparison->goal_amount) {
                // Find the specific expense by id and delete it
                Expense::find($comparison->id)->delete();
            }
        }

        try {
            return redirect('user_goalsetting')->with('success', [
                'message' => 'Amount updated Successfully',
                'duration' => 3000,
            ]);
        } catch (\Exception $e) {
            Log::error('Blog creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', [
                'message' => 'Failed to create blog. Error: ' . $e->getMessage(),
                'duration' => 5000,
            ])->withInput();
        };
    }

    public function calculateProjectedDates($goals)
    {
        foreach ($goals as $goal) {
            // Calculate the projected completion date
            if ($goal->current_amount < $goal->goal_amount) {
                $updatedAt = new DateTime($goal->updated_at);
                $startDate = new DateTime($goal->start_date);
                $daysBetweenAdds = $updatedAt->diff($startDate)->days;
                $remainingAmount = $goal->goal_amount - $goal->current_amount;

                // Check if last_added_amount is not zero
                if ($goal->last_added_amount != 0) {
                    $daysToComplete = $remainingAmount / $goal->last_added_amount * $daysBetweenAdds;
                    $projectedCompleteDate = $updatedAt->modify('+' . $daysToComplete . ' days');
                    $goal->projected_attainment_date = $projectedCompleteDate->format('Y-m-d');
                } else {
                    // Handle the case when last_added_amount is zero
                    $goal->projected_attainment_date = null;
                }
            }
        }
    }


    public function classifyGoals($goals)
    {
        foreach ($goals as $goal) {
            $createdAt = Carbon::parse($goal->created_at);
            $deadline = Carbon::parse($goal->deadline);
            $durationInDays = $createdAt->diffInDays($deadline);

            $shortTermThreshold = 30;
            $middleTermThreshold = 180;

            if ($durationInDays <= $shortTermThreshold) {
                $goal->period = 'short-term';
            } elseif ($durationInDays <= $middleTermThreshold) {
                $goal->period = 'medium-term';
            } else {
                $goal->period = 'long-term';
            }

            $goal->save();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $goal = Goal::findOrFail($id);
        $goal->delete();

        // Find the expense that matches the debt name for the current user
        $expense = Expense::where('expense_type', $goal->title)
            ->where('user_id', auth()->id())
            ->where('actual_expense', $goal->current_amount)
            ->first();
        $expense->delete();

        return redirect()->route('user_goalsetting')->with('success', [
            'message' => 'Goal Deleted Successfully!',
            'duration' => 3000,
        ]);
    }
}
