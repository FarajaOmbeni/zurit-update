<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\QuestionnaireResponseMail;

class QuestionnaireController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return Inertia::render('UserDashboard/Questionnaires', [
            'user' => $user
        ]);
    }

    public function submitQuestionnaire(Request $request)
    {
        // Get all form data
        $formData = $request->all();

        // If this is a money quiz, set the form type if not already set
        if (!isset($formData['form_type']) && isset($formData['goalSetting1'])) {
            $formData['form_type'] = 'Money Quiz - Wealth Score Assessment';
        }

        // Send email to admin
        Mail::to(config('services.email.admin_email'))->send(new QuestionnaireResponseMail($formData));

        // Send copy to user
        if (!empty($formData['email'])) {
            Mail::to($formData['email'])->send(new QuestionnaireResponseMail($formData));
        }

        // You can add a success message or redirect here if needed
        return redirect()->back()->with('success', 'Questionnaire submitted successfully! A copy has been sent to your email and we will be in touch.');
    }

    public function submitOnboarding(Request $request)
    {
        // Get all form data
        $formData = $request->all();
        $formData['form_type'] = 'Client Onboarding';

        // Send email to admin
        Mail::to(config('services.email.admin_email'))->send(new QuestionnaireResponseMail($formData));

        // Send copy to user
        if (!empty($formData['email'])) {
            Mail::to($formData['email'])->send(new QuestionnaireResponseMail($formData));
        }

        return redirect()->back()->with('success', 'Onboarding form submitted successfully! A copy has been sent to your email and we will be in touch.');
    }

    public function submitPersonality(Request $request)
    {
        // Get all form data
        $formData = $request->all();
        $formData['form_type'] = 'Money Personality Assessment';

        // Send email to admin
        Mail::to(config('services.email.admin_email'))->send(new QuestionnaireResponseMail($formData));

        // Send copy to user
        if (!empty($formData['email'])) {
            Mail::to($formData['email'])->send(new QuestionnaireResponseMail($formData));
        }

        return redirect()->back()->with('success', 'Money personality assessment submitted successfully! A copy has been sent to your email and we will be in touch.');
    }

    public function submitRiskTolerance(Request $request)
    {
        // Get all form data
        $formData = $request->all();
        $formData['form_type'] = 'Risk Tolerance Assessment';

        // Calculate risk score
        $totalScore = 0;
        for ($i = 1; $i <= 20; $i++) {
            $totalScore += intval($request->input("q{$i}", 0));
        }
        $formData['total_score'] = $totalScore;

        // Determine risk profile
        if ($totalScore >= 65) {
            $formData['risk_profile'] = 'Aggressive Investor';
        } elseif ($totalScore >= 45) {
            $formData['risk_profile'] = 'Moderate Investor';
        } elseif ($totalScore >= 25) {
            $formData['risk_profile'] = 'Conservative Investor';
        } else {
            $formData['risk_profile'] = 'Ultra Conservative';
        }

        // Send email to admin
        Mail::to(config('services.email.admin_email'))->send(new QuestionnaireResponseMail($formData));

        // Send copy to user
        if (!empty($formData['email'])) {
            Mail::to($formData['email'])->send(new QuestionnaireResponseMail($formData));
        }

        return redirect()->back()->with('success', 'Risk tolerance assessment submitted successfully! A copy has been sent to your email and we will be in touch.');
    }

    public function submitNextStep(Request $request)
    {
        // Get all form data
        $formData = $request->all();
        $formData['form_type'] = 'Next Natural Step Assessment';

        // Parse the checkbox arrays and calculate counts
        $counts = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];
        
        for ($i = 1; $i <= 10; $i++) {
            $answers = $request->input("q{$i}", []);
            if (is_array($answers)) {
                foreach ($answers as $answer) {
                    if (array_key_exists($answer, $counts)) {
                        $counts[$answer]++;
                    }
                }
            }
        }

        // Store counts in form data
        $formData['countA'] = $counts['A'];
        $formData['countB'] = $counts['B'];
        $formData['countC'] = $counts['C'];
        $formData['countD'] = $counts['D'];
        $formData['countE'] = $counts['E'];

        // Determine dominant persona
        $maxCount = max($counts);
        $dominantLetter = array_search($maxCount, $counts);

        $personas = [
            'A' => 'Systems Thinker',
            'B' => 'Communicator',
            'C' => 'Mentor/Trainer',
            'D' => 'Connector/Caregiver',
            'E' => 'Burnout Watch'
        ];

        $descriptions = [
            'A' => 'Strong at planning, organizing, budgeting, and problem-solving. Potential paths: admin support, budgeting coach, operations assistant.',
            'B' => 'Persuasive and confident with people. Paths: customer service trainer, micro-consulting, sales support.',
            'C' => 'Strong in coaching, tutoring, and guiding others. Paths: tutoring, interview prep, life skills trainer.',
            'D' => 'Helpful, nurturing, and service-minded. Paths: event support, hospitality, child care, wellness.',
            'E' => 'Feeling stuck or tired. Needs rest, mindset work, and energy budgeting before starting something new.'
        ];

        $formData['persona'] = $personas[$dominantLetter] ?? 'Balanced Profile';
        $formData['description'] = $descriptions[$dominantLetter] ?? 'You have a balanced profile across multiple areas.';

        // Send email to admin
        Mail::to(config('services.email.admin_email'))->send(new QuestionnaireResponseMail($formData));

        // Send copy to user
        if (!empty($formData['email'])) {
            Mail::to($formData['email'])->send(new QuestionnaireResponseMail($formData));
        }

        return redirect()->back()->with('success', 'Next Natural Step assessment submitted successfully! A copy has been sent to your email and we will be in touch.');
    }
}
