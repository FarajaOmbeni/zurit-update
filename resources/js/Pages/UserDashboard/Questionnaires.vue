<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import { reactive, ref, computed } from 'vue'

const props = defineProps({
    user: Object
})

// Onboarding Form Data
const onboardingForm = reactive({
    // Contact Information (auto-populated from auth user)
    fullName: props.user.name || '',
    email: props.user.email || '',
    phone: '',
    // Section A
    dateOfBirth: '', maritalStatus: '', childrenDependents: '',
    occupation: '', monthlyIncome: '', monthlyExpenses: '', residentialStatus: '',
    // Section B
    financeTracking: '', hasBudget: '', financialComfort: '', hadAdvisor: '', discussMoney: '',
    // Section C
    assets: [], loans: '', emergencyFund: '',
    // Section D
    goal1: '', goal2: '', goal3: '', goal1Timeline: '', goal2Timeline: '', goal3Timeline: '',
    motivation: '', fears: '',
    // Section E
    advisoryExpectations: [], communicationPreference: '', upcomingEvents: ''
})

// Money Personality Form Data
const personalityForm = reactive({
    // Contact Information (auto-populated from auth user)
    fullName: props.user.name || '',
    email: props.user.email || '',
    phone: '',
    q1: '', q2: '', q3: '', q4: '', q5: '', q6: '', q7: '', q8: '', q9: '', q10: '',
    q11: '', q12: '', q13: '', q14: '', q15: '', q16: '', q17: '', q18: '', q19: '', q20: ''
})

// Risk Tolerance Data
const riskForm = reactive({
    // Contact Information (auto-populated from auth user)
    fullName: props.user.name || '',
    email: props.user.email || '',
    phone: '',
    q1: 0, q2: 0, q3: 0, q4: 0, q5: 0, q6: 0, q7: 0, q8: 0, q9: 0, q10: 0,
    q11: 0, q12: 0, q13: 0, q14: 0, q15: 0, q16: 0, q17: 0, q18: 0, q19: 0, q20: 0
})

// Money Quiz Data
const quizScores = ref({
    goalSetting1: 0, goalSetting2: 0, investmentPlanning1: 0, investmentPlanning2: 0,
    debtManagement1: 0, debtManagement2: 0, budgetPlanning1: 0, budgetPlanning2: 0,
    financialKnowledge1: 0, financialKnowledge2: 0,
})

const userInfo = ref({
    name: props.user.name || '',
    email: props.user.email || '',
    phone: ''
})

// Results and modals
const showOnboardingResult = ref(false)
const showPersonalityResult = ref(false)
const showRiskResult = ref(false)
const showQuizModal = ref(false)

// Computed values
const totalQuizScore = computed(() => Object.values(quizScores.value).reduce((acc, score) => acc + parseInt(score), 0))
const quizResultTitle = computed(() => {
    if (totalQuizScore.value >= 21) return '💎 Wealth Master'
    if (totalQuizScore.value >= 11) return '📈 Growing Investor'
    return '🚦 Financial Starter'
})
const quizResultMessage = computed(() => {
    if (totalQuizScore.value >= 21) return 'You have excellent financial habits and are on track to achieving financial security and success. Keep up the great work!'
    if (totalQuizScore.value >= 11) return 'You are making good financial decisions but need to fine-tune your savings, investments, and budgeting strategies to build more wealth.'
    return 'You need to improve your financial planning, budgeting, and investment strategies. Start by setting clear financial goals and learning more about managing money.'
})

const totalRiskScore = computed(() => {
    let total = 0
    for (let i = 1; i <= 20; i++) {
        total += parseInt(riskForm[`q${i}`]) || 0
    }
    return total
})
const riskProfile = computed(() => {
    if (totalRiskScore.value >= 65) return '🚀 Aggressive Investor'
    if (totalRiskScore.value >= 45) return '📈 Moderate Investor'
    if (totalRiskScore.value >= 25) return '🛡️ Conservative Investor'
    return '💎 Ultra Conservative'
})

// Form submission functions
function submitOnboarding() {
    const form = useForm(onboardingForm)
    form.post(route('questionnaires.onboarding'), {
        onSuccess: () => {
            showOnboardingResult.value = true
            alert('Onboarding form submitted successfully!')
        }
    })
}

function submitPersonality() {
    const form = useForm(personalityForm)
    form.post(route('questionnaires.personality'), {
        onSuccess: () => {
            showPersonalityResult.value = true
            alert('Money personality assessment submitted successfully!')
        }
    })
}

function submitRiskTolerance() {
    const form = useForm(riskForm)
    form.post(route('questionnaires.risk'), {
        onSuccess: () => {
            showRiskResult.value = true
        }
    })
}

function submitMoneyQuiz() {
    showQuizModal.value = true
}

function contactUs() {
    const form = useForm({
        name: userInfo.value.name,
        email: userInfo.value.email,
        phone: userInfo.value.phone,
        message: quizResultMessage.value,
    })
    form.post(route('submit.quiz'), {
        onSuccess: () => {
            showQuizModal.value = false
            alert('Your information has been sent successfully! We will get back to you soon.')
        }
    })
}
</script>

<template>

    <Head title="Questionnaires" />
    <AuthenticatedLayout>
        <div class="w-full text-gray-900">
            <Sidebar>
                <div class="min-h-screen bg-white p-6 space-y-10">
                    <section class="max-w-6xl mx-auto space-y-6">
                        <h1 class="text-center text-4xl font-bold text-purple-600 mb-8">Questionnaires</h1>

                        <!-- 1. Onboarding Form -->
                        <details class="border rounded">
                            <summary class="cursor-pointer select-none p-4 bg-purple-700 text-white font-semibold">
                                1. Client Onboarding Questionnaire
                            </summary>
                            <div class="p-6 space-y-6">
                                <p class="text-sm text-gray-600 mb-4">Session 1: Discovery & Relationship Building -
                                    Confidential – For internal use only</p>

                                <!-- Section A -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-bold text-purple-800 mb-4">👤 SECTION A: PERSONAL & FAMILY
                                        PROFILE</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <label class="block">
                                            <span class="text-sm font-semibold">1. Full Name:</span>
                                            <input v-model="onboardingForm.fullName" type="text"
                                                class="w-full border p-2 rounded mt-1" />
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-semibold">2. Age Range:</span>
                                            <select v-model="onboardingForm.dateOfBirth"
                                                class="w-full border p-2 rounded mt-1">
                                                <option value="">Select age range...</option>
                                                <option value="18-25">18-25 years</option>
                                                <option value="26-35">26-35 years</option>
                                                <option value="36-45">36-45 years</option>
                                                <option value="46-55">46-55 years</option>
                                                <option value="56-65">56-65 years</option>
                                                <option value="65+">65+ years</option>
                                            </select>
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-semibold">3. Marital Status:</span>
                                            <select v-model="onboardingForm.maritalStatus"
                                                class="w-full border p-2 rounded mt-1">
                                                <option value="">Select...</option>
                                                <option value="single">Single</option>
                                                <option value="married">Married</option>
                                                <option value="divorced">Divorced</option>
                                                <option value="widowed">Widowed</option>
                                            </select>
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-semibold">4. Number of Children/Dependents:</span>
                                            <select v-model="onboardingForm.childrenDependents"
                                                class="w-full border p-2 rounded mt-1">
                                                <option value="">Select number...</option>
                                                <option value="0">0</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5+">5 or more</option>
                                            </select>
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-semibold">5. Occupation / Business Type:</span>
                                            <select v-model="onboardingForm.occupation"
                                                class="w-full border p-2 rounded mt-1">
                                                <option value="">Select occupation...</option>
                                                <option value="Accountant">Accountant</option>
                                                <option value="Engineer">Engineer</option>
                                                <option value="Teacher/Lecturer">Teacher/Lecturer</option>
                                                <option value="Doctor/Nurse">Doctor/Nurse</option>
                                                <option value="Lawyer">Lawyer</option>
                                                <option value="Business Owner">Business Owner</option>
                                                <option value="Manager/Executive">Manager/Executive</option>
                                                <option value="Sales Representative">Sales Representative</option>
                                                <option value="Consultant">Consultant</option>
                                                <option value="Government Employee">Government Employee</option>
                                                <option value="IT Professional">IT Professional</option>
                                                <option value="Banker/Financial Services">Banker/Financial Services
                                                </option>
                                                <option value="Student">Student</option>
                                                <option value="Retired">Retired</option>
                                                <option value="Self-employed">Self-employed</option>
                                                <option value="Farmer">Farmer</option>
                                                <option value="Unemployed">Unemployed</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-semibold">6. Monthly Net Income (KES):</span>
                                            <select v-model="onboardingForm.monthlyIncome"
                                                class="w-full border p-2 rounded mt-1">
                                                <option value="">Select income range...</option>
                                                <option value="Below 20,000">Below KES 20,000</option>
                                                <option value="20,000 - 50,000">KES 20,000 - 50,000</option>
                                                <option value="50,001 - 100,000">KES 50,001 - 100,000</option>
                                                <option value="100,001 - 200,000">KES 100,001 - 200,000</option>
                                                <option value="200,001 - 500,000">KES 200,001 - 500,000</option>
                                                <option value="500,001 - 1,000,000">KES 500,001 - 1,000,000</option>
                                                <option value="Above 1,000,000">Above KES 1,000,000</option>
                                            </select>
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-semibold">7. Monthly Expenses (KES):</span>
                                            <select v-model="onboardingForm.monthlyExpenses"
                                                class="w-full border p-2 rounded mt-1">
                                                <option value="">Select expense range...</option>
                                                <option value="Below 10,000">Below KES 10,000</option>
                                                <option value="10,000 - 25,000">KES 10,000 - 25,000</option>
                                                <option value="25,001 - 50,000">KES 25,001 - 50,000</option>
                                                <option value="50,001 - 100,000">KES 50,001 - 100,000</option>
                                                <option value="100,001 - 200,000">KES 100,001 - 200,000</option>
                                                <option value="200,001 - 500,000">KES 200,001 - 500,000</option>
                                                <option value="Above 500,000">Above KES 500,000</option>
                                            </select>
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-semibold">8. Location/County:</span>
                                            <select v-model="onboardingForm.residentialStatus"
                                                class="w-full border p-2 rounded mt-1">
                                                <option value="">Select location...</option>
                                                <option value="Nairobi">Nairobi</option>
                                                <option value="Mombasa">Mombasa</option>
                                                <option value="Kisumu">Kisumu</option>
                                                <option value="Nakuru">Nakuru</option>
                                                <option value="Eldoret">Eldoret</option>
                                                <option value="Thika">Thika</option>
                                                <option value="Nyeri">Nyeri</option>
                                                <option value="Meru">Meru</option>
                                                <option value="Kakamega">Kakamega</option>
                                                <option value="Machakos">Machakos</option>
                                                <option value="Other County">Other County</option>
                                                <option value="Outside Kenya">Outside Kenya</option>
                                            </select>
                                        </label>
                                    </div>
                                </div>

                                <!-- Section B -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-bold text-purple-800 mb-4">💡 SECTION B: FINANCIAL HABITS &
                                        KNOWLEDGE</h3>
                                    <div class="space-y-4">
                                        <label class="block">
                                            <span class="text-sm font-semibold">9. How do you currently track your
                                                finances?</span>
                                            <select v-model="onboardingForm.financeTracking"
                                                class="w-full border p-2 rounded mt-1">
                                                <option value="">Select tracking method...</option>
                                                <option value="Mobile app">Mobile app (Excel, Google Sheets, etc.)
                                                </option>
                                                <option value="Spreadsheet">Computer spreadsheet</option>
                                                <option value="Notebook/Paper">Notebook/Paper</option>
                                                <option value="Bank statements">Bank statements only</option>
                                                <option value="Mental tracking">Mental tracking</option>
                                                <option value="Don't track">Don't track</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-semibold">10. Do you maintain a monthly or annual
                                                budget?</span>
                                            <select v-model="onboardingForm.hasBudget"
                                                class="w-full border p-2 rounded mt-1">
                                                <option value="">Select...</option>
                                                <option value="yes">Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-semibold">11. How comfortable are you with making
                                                financial decisions?</span>
                                            <select v-model="onboardingForm.financialComfort"
                                                class="w-full border p-2 rounded mt-1">
                                                <option value="">Select...</option>
                                                <option value="very">Very Comfortable</option>
                                                <option value="somewhat">Somewhat Comfortable</option>
                                                <option value="not">Not Comfortable</option>
                                            </select>
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-semibold">12. Have you ever worked with a
                                                financial advisor before?</span>
                                            <select v-model="onboardingForm.hadAdvisor"
                                                class="w-full border p-2 rounded mt-1">
                                                <option value="">Select...</option>
                                                <option value="yes">Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-semibold">13. Do you discuss money regularly with
                                                your spouse/family?</span>
                                            <select v-model="onboardingForm.discussMoney"
                                                class="w-full border p-2 rounded mt-1">
                                                <option value="">Select...</option>
                                                <option value="yes">Yes</option>
                                                <option value="no">No</option>
                                                <option value="na">Not applicable</option>
                                            </select>
                                        </label>
                                    </div>
                                </div>

                                <!-- Section C -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-bold text-purple-800 mb-4">📈 SECTION C: ASSETS, LIABILITIES
                                        & SAVINGS</h3>
                                    <div class="space-y-4">
                                        <label class="block">
                                            <span class="text-sm font-semibold">14. Do you currently have any of the
                                                following? (Check all that apply)</span>
                                            <div class="grid grid-cols-2 gap-2 mt-2">
                                                <label class="flex items-center"><input type="checkbox"
                                                        value="bank_savings" v-model="onboardingForm.assets"
                                                        class="mr-2"> Bank savings account</label>
                                                <label class="flex items-center"><input type="checkbox"
                                                        value="money_market" v-model="onboardingForm.assets"
                                                        class="mr-2"> Money Market Fund</label>
                                                <label class="flex items-center"><input type="checkbox" value="sacco"
                                                        v-model="onboardingForm.assets" class="mr-2"> SACCO
                                                    shares</label>
                                                <label class="flex items-center"><input type="checkbox" value="chama"
                                                        v-model="onboardingForm.assets" class="mr-2"> Chama
                                                    contributions</label>
                                                <label class="flex items-center"><input type="checkbox" value="property"
                                                        v-model="onboardingForm.assets" class="mr-2"> Investment
                                                    property</label>
                                                <label class="flex items-center"><input type="checkbox" value="business"
                                                        v-model="onboardingForm.assets" class="mr-2"> Business</label>
                                                <label class="flex items-center"><input type="checkbox" value="pension"
                                                        v-model="onboardingForm.assets" class="mr-2"> Pension plan /
                                                    Retirement fund</label>
                                                <label class="flex items-center"><input type="checkbox"
                                                        value="insurance" v-model="onboardingForm.assets" class="mr-2">
                                                    Insurance (Life/Medical/Education)</label>
                                                <label class="flex items-center"><input type="checkbox" value="stocks"
                                                        v-model="onboardingForm.assets" class="mr-2"> Stocks or
                                                    Bonds</label>
                                                <label class="flex items-center"><input type="checkbox" value="other"
                                                        v-model="onboardingForm.assets" class="mr-2"> Other
                                                    investments</label>
                                            </div>
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-semibold">15. Do you have any ongoing loans or
                                                debts?</span>
                                            <textarea v-model="onboardingForm.loans"
                                                class="w-full border p-2 rounded mt-1"
                                                placeholder="If yes, please describe type, amount, and repayment status"></textarea>
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-semibold">16. Do you have an emergency fund (3–6
                                                months of expenses)?</span>
                                            <select v-model="onboardingForm.emergencyFund"
                                                class="w-full border p-2 rounded mt-1">
                                                <option value="">Select...</option>
                                                <option value="yes">Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </label>
                                    </div>
                                </div>

                                <!-- Section D -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-bold text-purple-800 mb-4">🎯 SECTION D: GOALS & PRIORITIES
                                    </h3>
                                    <div class="space-y-4">
                                        <label class="block">
                                            <span class="text-sm font-semibold">17. What are your top 3 financial goals
                                                right now?</span>
                                            <input v-model="onboardingForm.goal1" type="text"
                                                class="w-full border p-2 rounded mt-1" placeholder="Goal 1" />
                                            <input v-model="onboardingForm.goal2" type="text"
                                                class="w-full border p-2 rounded mt-1" placeholder="Goal 2" />
                                            <input v-model="onboardingForm.goal3" type="text"
                                                class="w-full border p-2 rounded mt-1" placeholder="Goal 3" />
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-semibold">18. When would you like to achieve each
                                                goal?</span>
                                            <select v-model="onboardingForm.goal1Timeline"
                                                class="w-full border p-2 rounded mt-1 mb-2">
                                                <option value="">Goal 1 timeline...</option>
                                                <option value="Within 6 months">Within 6 months</option>
                                                <option value="6-12 months">6-12 months</option>
                                                <option value="1-2 years">1-2 years</option>
                                                <option value="3-5 years">3-5 years</option>
                                                <option value="5-10 years">5-10 years</option>
                                                <option value="Over 10 years">Over 10 years</option>
                                            </select>
                                            <select v-model="onboardingForm.goal2Timeline"
                                                class="w-full border p-2 rounded mt-1 mb-2">
                                                <option value="">Goal 2 timeline...</option>
                                                <option value="Within 6 months">Within 6 months</option>
                                                <option value="6-12 months">6-12 months</option>
                                                <option value="1-2 years">1-2 years</option>
                                                <option value="3-5 years">3-5 years</option>
                                                <option value="5-10 years">5-10 years</option>
                                                <option value="Over 10 years">Over 10 years</option>
                                            </select>
                                            <select v-model="onboardingForm.goal3Timeline"
                                                class="w-full border p-2 rounded mt-1">
                                                <option value="">Goal 3 timeline...</option>
                                                <option value="Within 6 months">Within 6 months</option>
                                                <option value="6-12 months">6-12 months</option>
                                                <option value="1-2 years">1-2 years</option>
                                                <option value="3-5 years">3-5 years</option>
                                                <option value="5-10 years">5-10 years</option>
                                                <option value="Over 10 years">Over 10 years</option>
                                            </select>
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-semibold">19. What motivates you to pursue
                                                financial independence or growth?</span>
                                            <textarea v-model="onboardingForm.motivation"
                                                class="w-full border p-2 rounded mt-1"></textarea>
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-semibold">20. What worries or fears do you have
                                                about your financial future?</span>
                                            <textarea v-model="onboardingForm.fears"
                                                class="w-full border p-2 rounded mt-1"></textarea>
                                        </label>
                                    </div>
                                </div>

                                <!-- Section E -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-bold text-purple-800 mb-4">🧭 SECTION E: PREFERENCES &
                                        EXPECTATIONS</h3>
                                    <div class="space-y-4">
                                        <label class="block">
                                            <span class="text-sm font-semibold">21. What would you like from this
                                                advisory relationship? (Check all that apply)</span>
                                            <div class="grid grid-cols-1 gap-2 mt-2">
                                                <label class="flex items-center"><input type="checkbox" value="clarity"
                                                        v-model="onboardingForm.advisoryExpectations" class="mr-2">
                                                    Clarity on my finances</label>
                                                <label class="flex items-center"><input type="checkbox" value="plan"
                                                        v-model="onboardingForm.advisoryExpectations" class="mr-2"> A
                                                    step-by-step plan</label>
                                                <label class="flex items-center"><input type="checkbox"
                                                        value="investment" v-model="onboardingForm.advisoryExpectations"
                                                        class="mr-2"> Investment guidance</label>
                                                <label class="flex items-center"><input type="checkbox"
                                                        value="budgeting" v-model="onboardingForm.advisoryExpectations"
                                                        class="mr-2"> Budgeting & saving help</label>
                                                <label class="flex items-center"><input type="checkbox"
                                                        value="accountability"
                                                        v-model="onboardingForm.advisoryExpectations" class="mr-2">
                                                    Accountability and regular check-ins</label>
                                                <label class="flex items-center"><input type="checkbox" value="other"
                                                        v-model="onboardingForm.advisoryExpectations" class="mr-2">
                                                    Other</label>
                                            </div>
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-semibold">22. Preferred communication style &
                                                frequency:</span>
                                            <select v-model="onboardingForm.communicationPreference"
                                                class="w-full border p-2 rounded mt-1">
                                                <option value="">Select...</option>
                                                <option value="monthly_inperson">Monthly check-ins - In-person</option>
                                                <option value="monthly_virtual">Monthly check-ins - Virtual</option>
                                                <option value="monthly_whatsapp">Monthly check-ins - WhatsApp</option>
                                                <option value="monthly_email">Monthly check-ins - Email</option>
                                                <option value="quarterly_inperson">Quarterly reviews - In-person
                                                </option>
                                                <option value="quarterly_virtual">Quarterly reviews - Virtual</option>
                                                <option value="quarterly_whatsapp">Quarterly reviews - WhatsApp</option>
                                                <option value="quarterly_email">Quarterly reviews - Email</option>
                                            </select>
                                        </label>
                                        <label class="block">
                                            <span class="text-sm font-semibold">23. Any upcoming major life events we
                                                should consider?</span>
                                            <textarea v-model="onboardingForm.upcomingEvents"
                                                class="w-full border p-2 rounded mt-1"
                                                placeholder="e.g., wedding, childbirth, business expansion, relocation, education"></textarea>
                                        </label>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                    <h3 class="text-lg font-bold text-purple-800 mb-4">📞 Contact Information</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="col-span-1">
                                            <span class="text-sm font-semibold">Name:</span>
                                            <input v-model="onboardingForm.fullName" type="text" readonly
                                                class="w-full border p-2 rounded mt-1 bg-gray-100" />
                                        </div>
                                        <div class="col-span-1">
                                            <span class="text-sm font-semibold">Email:</span>
                                            <input v-model="onboardingForm.email" type="email" readonly
                                                class="w-full border p-2 rounded mt-1 bg-gray-100" />
                                        </div>
                                        <div class="col-span-1">
                                            <span class="text-sm font-semibold">Phone Number: <span
                                                    class="text-red-500">*</span></span>
                                            <input v-model="onboardingForm.phone" type="tel" required
                                                class="w-full border p-2 rounded mt-1"
                                                placeholder="Enter your phone number" />
                                        </div>
                                    </div>
                                </div>

                                <button @click="submitOnboarding"
                                    class="w-full bg-gradient-to-r from-purple-700 to-yellow-500 text-white px-6 py-3 font-bold rounded-lg hover:scale-105 transition-transform">
                                    Submit Onboarding Form
                                </button>
                            </div>
                        </details>

                        <!-- 2. Money Personality -->
                        <details class="border rounded">
                            <summary class="cursor-pointer select-none p-4 bg-purple-700 text-white font-semibold">
                                2. Money Personality Assessment
                            </summary>
                            <div class="p-6 space-y-4">
                                <p class="text-sm text-gray-600 mb-4">Choose the answer (A-E) that best describes your
                                    typical financial behavior or attitude.</p>

                                <div class="grid grid-cols-1 gap-4">
                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">1. When you receive unexpected money, your first
                                            instinct is:</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="personalityForm.q1"
                                                    type="radio" value="A" class="mr-2"> A. Save it immediately.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q1"
                                                    type="radio" value="B" class="mr-2"> B. Plan exactly how you'll
                                                allocate it.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q1"
                                                    type="radio" value="C" class="mr-2"> C. Treat yourself or
                                                others.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q1"
                                                    type="radio" value="D" class="mr-2"> D. Invest it into something
                                                promising.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q1"
                                                    type="radio" value="E" class="mr-2"> E. Worry about managing it
                                                properly.</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">2. Budgeting for you is:</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="personalityForm.q2"
                                                    type="radio" value="A" class="mr-2"> A. Essential and strictly
                                                followed.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q2"
                                                    type="radio" value="B" class="mr-2"> B. Carefully planned out each
                                                month.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q2"
                                                    type="radio" value="C" class="mr-2"> C. A guideline you loosely
                                                follow.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q2"
                                                    type="radio" value="D" class="mr-2"> D. Flexible, depending on
                                                investment opportunities.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q2"
                                                    type="radio" value="E" class="mr-2"> E. Something you tend to
                                                avoid.</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">3. How do you feel about financial risk?</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="personalityForm.q3"
                                                    type="radio" value="A" class="mr-2"> A. Prefer safety over
                                                risks.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q3"
                                                    type="radio" value="B" class="mr-2"> B. Comfortable only after
                                                careful analysis.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q3"
                                                    type="radio" value="C" class="mr-2"> C. Indifferent if it brings
                                                enjoyment.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q3"
                                                    type="radio" value="D" class="mr-2"> D. Embrace it for potential
                                                gains.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q3"
                                                    type="radio" value="E" class="mr-2"> E. Nervous and unsure.</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">4. How often do you review your financial goals?
                                        </p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="personalityForm.q4"
                                                    type="radio" value="A" class="mr-2"> A. Regularly, to ensure savings
                                                are growing.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q4"
                                                    type="radio" value="B" class="mr-2"> B. Monthly or quarterly,
                                                following your plan.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q4"
                                                    type="radio" value="C" class="mr-2"> C. Rarely, as long as bills are
                                                paid.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q4"
                                                    type="radio" value="D" class="mr-2"> D. Frequently, adjusting for
                                                market trends.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q4"
                                                    type="radio" value="E" class="mr-2"> E. Hardly ever.</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">5. When faced with a financial decision, you:</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="personalityForm.q5"
                                                    type="radio" value="A" class="mr-2"> A. Consider the safest
                                                option.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q5"
                                                    type="radio" value="B" class="mr-2"> B. Research thoroughly before
                                                deciding.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q5"
                                                    type="radio" value="C" class="mr-2"> C. Usually go with what feels
                                                good now.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q5"
                                                    type="radio" value="D" class="mr-2"> D. Look for long-term potential
                                                benefits.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q5"
                                                    type="radio" value="E" class="mr-2"> E. Procrastinate and avoid the
                                                decision.</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">6. What's your view on debt?</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="personalityForm.q6"
                                                    type="radio" value="A" class="mr-2"> A. Avoid it completely.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q6"
                                                    type="radio" value="B" class="mr-2"> B. Acceptable if carefully
                                                managed.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q6"
                                                    type="radio" value="C" class="mr-2"> C. A normal part of
                                                life.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q6"
                                                    type="radio" value="D" class="mr-2"> D. Useful for leverage in
                                                investments.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q6"
                                                    type="radio" value="E" class="mr-2"> E. Overwhelming and
                                                stressful.</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">7. How organized are your financial records?</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="personalityForm.q7"
                                                    type="radio" value="A" class="mr-2"> A. Meticulously
                                                organized.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q7"
                                                    type="radio" value="B" class="mr-2"> B. Well-structured with clear
                                                plans.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q7"
                                                    type="radio" value="C" class="mr-2"> C. Basic and mostly
                                                digital.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q7"
                                                    type="radio" value="D" class="mr-2"> D. Detailed regarding
                                                investments.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q7"
                                                    type="radio" value="E" class="mr-2"> E. Unorganized or
                                                nonexistent.</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">8. Your primary goal with money is:</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="personalityForm.q8"
                                                    type="radio" value="A" class="mr-2"> A. Security and peace of
                                                mind.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q8"
                                                    type="radio" value="B" class="mr-2"> B. Achieving clear financial
                                                milestones.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q8"
                                                    type="radio" value="C" class="mr-2"> C. Enjoying life and
                                                experiences.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q8"
                                                    type="radio" value="D" class="mr-2"> D. Growing wealth
                                                steadily.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q8"
                                                    type="radio" value="E" class="mr-2"> E. Minimizing stress and
                                                complexity.</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">9. How comfortable are you discussing money?</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="personalityForm.q9"
                                                    type="radio" value="A" class="mr-2"> A. Prefer to keep private and
                                                controlled.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q9"
                                                    type="radio" value="B" class="mr-2"> B. Comfortable when
                                                organized.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q9"
                                                    type="radio" value="C" class="mr-2"> C. Open and casual.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q9"
                                                    type="radio" value="D" class="mr-2"> D. Confident discussing
                                                financial opportunities.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q9"
                                                    type="radio" value="E" class="mr-2"> E. Uncomfortable or
                                                avoidant.</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">10. Your reaction to financial news or market
                                            trends is usually:</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="personalityForm.q10"
                                                    type="radio" value="A" class="mr-2"> A. Uninterested unless it
                                                impacts savings.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q10"
                                                    type="radio" value="B" class="mr-2"> B. Interested for planning
                                                purposes.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q10"
                                                    type="radio" value="C" class="mr-2"> C. Neutral or
                                                indifferent.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q10"
                                                    type="radio" value="D" class="mr-2"> D. Highly engaged and
                                                proactive.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q10"
                                                    type="radio" value="E" class="mr-2"> E. Overwhelmed or
                                                confused.</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">11. If you encounter financial difficulties, you:
                                        </p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="personalityForm.q11"
                                                    type="radio" value="A" class="mr-2"> A. Rely on savings
                                                immediately.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q11"
                                                    type="radio" value="B" class="mr-2"> B. Review and adjust your
                                                plan.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q11"
                                                    type="radio" value="C" class="mr-2"> C. Cut back on luxuries
                                                temporarily.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q11"
                                                    type="radio" value="D" class="mr-2"> D. Find new investment
                                                solutions.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q11"
                                                    type="radio" value="E" class="mr-2"> E. Feel helpless or
                                                stuck.</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">12. Retirement planning for you is:</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="personalityForm.q12"
                                                    type="radio" value="A" class="mr-2"> A. Essential, with clear
                                                savings targets.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q12"
                                                    type="radio" value="B" class="mr-2"> B. Well-planned and regularly
                                                reviewed.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q12"
                                                    type="radio" value="C" class="mr-2"> C. Something to think about
                                                later.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q12"
                                                    type="radio" value="D" class="mr-2"> D. Opportunity to grow wealth
                                                through investing.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q12"
                                                    type="radio" value="E" class="mr-2"> E. Daunting and
                                                stressful.</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">13. Spending money on yourself feels:</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="personalityForm.q13"
                                                    type="radio" value="A" class="mr-2"> A. Rare and
                                                unnecessary.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q13"
                                                    type="radio" value="B" class="mr-2"> B. Acceptable within your
                                                plan.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q13"
                                                    type="radio" value="C" class="mr-2"> C. Natural and
                                                enjoyable.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q13"
                                                    type="radio" value="D" class="mr-2"> D. Justified if it leads to
                                                future gains.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q13"
                                                    type="radio" value="E" class="mr-2"> E. Anxiety-inducing.</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">14. Your financial education comes primarily from:
                                        </p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="personalityForm.q14"
                                                    type="radio" value="A" class="mr-2"> A. Family values and saving
                                                habits.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q14"
                                                    type="radio" value="B" class="mr-2"> B. Books, courses, or
                                                professional advice.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q14"
                                                    type="radio" value="C" class="mr-2"> C. Personal
                                                experiences.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q14"
                                                    type="radio" value="D" class="mr-2"> D. Investment news and market
                                                analysis.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q14"
                                                    type="radio" value="E" class="mr-2"> E. Sporadic or minimal
                                                sources.</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">15. Your reaction to an impulsive purchase is
                                            usually:</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="personalityForm.q15"
                                                    type="radio" value="A" class="mr-2"> A. Regretful.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q15"
                                                    type="radio" value="B" class="mr-2"> B. Thoughtful and
                                                calculated.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q15"
                                                    type="radio" value="C" class="mr-2"> C. Satisfied and happy.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q15"
                                                    type="radio" value="D" class="mr-2"> D. Neutral—focus more on
                                                investments anyway.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q15"
                                                    type="radio" value="E" class="mr-2"> E. Worried about money
                                                management.</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">16. Credit cards to you are:</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="personalityForm.q16"
                                                    type="radio" value="A" class="mr-2"> A. A risk you prefer to
                                                avoid.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q16"
                                                    type="radio" value="B" class="mr-2"> B. Useful when managed
                                                carefully.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q16"
                                                    type="radio" value="C" class="mr-2"> C. Helpful and
                                                convenient.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q16"
                                                    type="radio" value="D" class="mr-2"> D. Tools for strategic
                                                advantage.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q16"
                                                    type="radio" value="E" class="mr-2"> E. Sources of stress.</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">17. Your ideal financial scenario involves:</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="personalityForm.q17"
                                                    type="radio" value="A" class="mr-2"> A. A comfortable emergency
                                                fund.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q17"
                                                    type="radio" value="B" class="mr-2"> B. Achieving clearly set
                                                financial goals.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q17"
                                                    type="radio" value="C" class="mr-2"> C. Enough to enjoy life
                                                comfortably.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q17"
                                                    type="radio" value="D" class="mr-2"> D. Growing investments and
                                                returns.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q17"
                                                    type="radio" value="E" class="mr-2"> E. Not worrying about
                                                money.</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">18. How do you feel about financial planning tools
                                            (apps/software)?</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="personalityForm.q18"
                                                    type="radio" value="A" class="mr-2"> A. Use them minimally to track
                                                savings.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q18"
                                                    type="radio" value="B" class="mr-2"> B. Regularly utilize them for
                                                budgeting.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q18"
                                                    type="radio" value="C" class="mr-2"> C. Occasionally glance at
                                                them.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q18"
                                                    type="radio" value="D" class="mr-2"> D. Frequently use for
                                                investment tracking.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q18"
                                                    type="radio" value="E" class="mr-2"> E. Avoid them.</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">19. Investing to you feels:</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="personalityForm.q19"
                                                    type="radio" value="A" class="mr-2"> A. Too risky or
                                                complex.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q19"
                                                    type="radio" value="B" class="mr-2"> B. Important, with careful
                                                planning.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q19"
                                                    type="radio" value="C" class="mr-2"> C. Unnecessary or
                                                confusing.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q19"
                                                    type="radio" value="D" class="mr-2"> D. Exciting and
                                                necessary.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q19"
                                                    type="radio" value="E" class="mr-2"> E. Intimidating.</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">20. When discussing finances with a partner or
                                            family, you:</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="personalityForm.q20"
                                                    type="radio" value="A" class="mr-2"> A. Prefer clear and cautious
                                                plans.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q20"
                                                    type="radio" value="B" class="mr-2"> B. Lead with detailed
                                                structure.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q20"
                                                    type="radio" value="C" class="mr-2"> C. Are relaxed and
                                                spontaneous.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q20"
                                                    type="radio" value="D" class="mr-2"> D. Encourage discussions about
                                                growth opportunities.</label>
                                            <label class="flex items-center"><input v-model="personalityForm.q20"
                                                    type="radio" value="E" class="mr-2"> E. Avoid conversations if
                                                possible.</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                    <h3 class="text-lg font-bold text-purple-800 mb-4">📞 Contact Information</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="col-span-1">
                                            <span class="text-sm font-semibold">Full Name:</span>
                                            <input v-model="personalityForm.fullName" type="text" readonly
                                                class="w-full border p-2 rounded mt-1 bg-gray-100" />
                                        </div>
                                        <div class="col-span-1">
                                            <span class="text-sm font-semibold">Email Address:</span>
                                            <input v-model="personalityForm.email" type="email" readonly
                                                class="w-full border p-2 rounded mt-1 bg-gray-100" />
                                        </div>
                                        <div class="col-span-1">
                                            <span class="text-sm font-semibold">Phone Number: <span
                                                    class="text-red-500">*</span></span>
                                            <input v-model="personalityForm.phone" type="tel" required
                                                class="w-full border p-2 rounded mt-1"
                                                placeholder="Enter your phone number" />
                                        </div>
                                    </div>
                                </div>

                                <button @click="submitPersonality"
                                    class="w-full bg-gradient-to-r from-purple-700 to-yellow-500 text-white px-6 py-3 font-bold rounded-lg hover:scale-105 transition-transform">
                                    Submit Personality Assessment
                                </button>
                            </div>
                        </details>

                        <!-- 3. Risk Tolerance -->
                        <details class="border rounded">
                            <summary class="cursor-pointer select-none p-4 bg-purple-700 text-white font-semibold">
                                3. Risk Tolerance Assessment
                            </summary>
                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">1. What is your current age?</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="riskForm.q1" type="radio"
                                                    :value="4" class="mr-2"> Below 30</label>
                                            <label class="flex items-center"><input v-model="riskForm.q1" type="radio"
                                                    :value="3" class="mr-2"> 30–45</label>
                                            <label class="flex items-center"><input v-model="riskForm.q1" type="radio"
                                                    :value="2" class="mr-2"> 46–60</label>
                                            <label class="flex items-center"><input v-model="riskForm.q1" type="radio"
                                                    :value="1" class="mr-2"> Above 60</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">2. What is your employment/income stability like?
                                        </p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="riskForm.q2" type="radio"
                                                    :value="4" class="mr-2"> Very stable</label>
                                            <label class="flex items-center"><input v-model="riskForm.q2" type="radio"
                                                    :value="3" class="mr-2"> Fairly stable</label>
                                            <label class="flex items-center"><input v-model="riskForm.q2" type="radio"
                                                    :value="2" class="mr-2"> Irregular</label>
                                            <label class="flex items-center"><input v-model="riskForm.q2" type="radio"
                                                    :value="1" class="mr-2"> Unstable or freelance</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">3. What is your monthly savings rate?</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="riskForm.q3" type="radio"
                                                    :value="4" class="mr-2"> Over 30% of income</label>
                                            <label class="flex items-center"><input v-model="riskForm.q3" type="radio"
                                                    :value="3" class="mr-2"> 20–30%</label>
                                            <label class="flex items-center"><input v-model="riskForm.q3" type="radio"
                                                    :value="2" class="mr-2"> 10–20%</label>
                                            <label class="flex items-center"><input v-model="riskForm.q3" type="radio"
                                                    :value="1" class="mr-2"> Below 10% or none</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">4. What percentage of your wealth is already
                                            invested?</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="riskForm.q4" type="radio"
                                                    :value="4" class="mr-2"> Over 60%</label>
                                            <label class="flex items-center"><input v-model="riskForm.q4" type="radio"
                                                    :value="3" class="mr-2"> 40–60%</label>
                                            <label class="flex items-center"><input v-model="riskForm.q4" type="radio"
                                                    :value="2" class="mr-2"> 20–40%</label>
                                            <label class="flex items-center"><input v-model="riskForm.q4" type="radio"
                                                    :value="1" class="mr-2"> Less than 20%</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">5. What is your primary financial goal?</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="riskForm.q5" type="radio"
                                                    :value="4" class="mr-2"> Capital growth</label>
                                            <label class="flex items-center"><input v-model="riskForm.q5" type="radio"
                                                    :value="3" class="mr-2"> Balanced growth and income</label>
                                            <label class="flex items-center"><input v-model="riskForm.q5" type="radio"
                                                    :value="2" class="mr-2"> Regular income</label>
                                            <label class="flex items-center"><input v-model="riskForm.q5" type="radio"
                                                    :value="1" class="mr-2"> Capital preservation</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">6. How would you describe your investment
                                            knowledge?</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="riskForm.q6" type="radio"
                                                    :value="4" class="mr-2"> Extensive</label>
                                            <label class="flex items-center"><input v-model="riskForm.q6" type="radio"
                                                    :value="3" class="mr-2"> Moderate</label>
                                            <label class="flex items-center"><input v-model="riskForm.q6" type="radio"
                                                    :value="2" class="mr-2"> Basic</label>
                                            <label class="flex items-center"><input v-model="riskForm.q6" type="radio"
                                                    :value="1" class="mr-2"> None</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">7. Have you previously made any investment
                                            decisions independently?</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="riskForm.q7" type="radio"
                                                    :value="4" class="mr-2"> Frequently</label>
                                            <label class="flex items-center"><input v-model="riskForm.q7" type="radio"
                                                    :value="3" class="mr-2"> Occasionally</label>
                                            <label class="flex items-center"><input v-model="riskForm.q7" type="radio"
                                                    :value="2" class="mr-2"> Rarely</label>
                                            <label class="flex items-center"><input v-model="riskForm.q7" type="radio"
                                                    :value="1" class="mr-2"> Never</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">8. Do you currently follow financial or investment
                                            news?</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="riskForm.q8" type="radio"
                                                    :value="4" class="mr-2"> Daily</label>
                                            <label class="flex items-center"><input v-model="riskForm.q8" type="radio"
                                                    :value="3" class="mr-2"> Weekly</label>
                                            <label class="flex items-center"><input v-model="riskForm.q8" type="radio"
                                                    :value="2" class="mr-2"> Occasionally</label>
                                            <label class="flex items-center"><input v-model="riskForm.q8" type="radio"
                                                    :value="1" class="mr-2"> Never</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">9. Have you ever invested in high-risk products
                                            (crypto, stocks, startups)?</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="riskForm.q9" type="radio"
                                                    :value="4" class="mr-2"> Yes, often</label>
                                            <label class="flex items-center"><input v-model="riskForm.q9" type="radio"
                                                    :value="3" class="mr-2"> Yes, a few times</label>
                                            <label class="flex items-center"><input v-model="riskForm.q9" type="radio"
                                                    :value="2" class="mr-2"> Tried once or twice</label>
                                            <label class="flex items-center"><input v-model="riskForm.q9" type="radio"
                                                    :value="1" class="mr-2"> Never</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">10. Do you understand the trade-off between risk
                                            and return?</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="riskForm.q10" type="radio"
                                                    :value="4" class="mr-2"> Very well</label>
                                            <label class="flex items-center"><input v-model="riskForm.q10" type="radio"
                                                    :value="3" class="mr-2"> Somewhat</label>
                                            <label class="flex items-center"><input v-model="riskForm.q10" type="radio"
                                                    :value="2" class="mr-2"> Not much</label>
                                            <label class="flex items-center"><input v-model="riskForm.q10" type="radio"
                                                    :value="1" class="mr-2"> Not at all</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">11. If your investment portfolio dropped 20% in 6
                                            months, what would you do?</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="riskForm.q11" type="radio"
                                                    :value="4" class="mr-2"> Buy more – good opportunity</label>
                                            <label class="flex items-center"><input v-model="riskForm.q11" type="radio"
                                                    :value="3" class="mr-2"> Hold and wait</label>
                                            <label class="flex items-center"><input v-model="riskForm.q11" type="radio"
                                                    :value="2" class="mr-2"> Re-evaluate and possibly reduce</label>
                                            <label class="flex items-center"><input v-model="riskForm.q11" type="radio"
                                                    :value="1" class="mr-2"> Sell to prevent more loss</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">12. How confident are you in sticking to a
                                            long-term plan despite market noise?</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="riskForm.q12" type="radio"
                                                    :value="4" class="mr-2"> Very confident</label>
                                            <label class="flex items-center"><input v-model="riskForm.q12" type="radio"
                                                    :value="3" class="mr-2"> Mostly confident</label>
                                            <label class="flex items-center"><input v-model="riskForm.q12" type="radio"
                                                    :value="2" class="mr-2"> Sometimes unsure</label>
                                            <label class="flex items-center"><input v-model="riskForm.q12" type="radio"
                                                    :value="1" class="mr-2"> Not confident</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">13. When making financial decisions, are you:</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="riskForm.q13" type="radio"
                                                    :value="4" class="mr-2"> Quick and confident</label>
                                            <label class="flex items-center"><input v-model="riskForm.q13" type="radio"
                                                    :value="3" class="mr-2"> Analytical but decisive</label>
                                            <label class="flex items-center"><input v-model="riskForm.q13" type="radio"
                                                    :value="2" class="mr-2"> Hesitant</label>
                                            <label class="flex items-center"><input v-model="riskForm.q13" type="radio"
                                                    :value="1" class="mr-2"> Prefer to defer</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">14. Have you ever sold an investment due to panic
                                            or fear?</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="riskForm.q14" type="radio"
                                                    :value="4" class="mr-2"> Never</label>
                                            <label class="flex items-center"><input v-model="riskForm.q14" type="radio"
                                                    :value="3" class="mr-2"> Rarely</label>
                                            <label class="flex items-center"><input v-model="riskForm.q14" type="radio"
                                                    :value="2" class="mr-2"> Sometimes</label>
                                            <label class="flex items-center"><input v-model="riskForm.q14" type="radio"
                                                    :value="1" class="mr-2"> Frequently</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">15. How do you feel about financial uncertainty?
                                        </p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="riskForm.q15" type="radio"
                                                    :value="4" class="mr-2"> Comfortable with it</label>
                                            <label class="flex items-center"><input v-model="riskForm.q15" type="radio"
                                                    :value="3" class="mr-2"> Acceptable if rewards are high</label>
                                            <label class="flex items-center"><input v-model="riskForm.q15" type="radio"
                                                    :value="2" class="mr-2"> Prefer to avoid it</label>
                                            <label class="flex items-center"><input v-model="riskForm.q15" type="radio"
                                                    :value="1" class="mr-2"> Very uncomfortable</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">16. What is your preferred investment time
                                            horizon?</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="riskForm.q16" type="radio"
                                                    :value="4" class="mr-2"> Over 10 years</label>
                                            <label class="flex items-center"><input v-model="riskForm.q16" type="radio"
                                                    :value="3" class="mr-2"> 5–10 years</label>
                                            <label class="flex items-center"><input v-model="riskForm.q16" type="radio"
                                                    :value="2" class="mr-2"> 3–5 years</label>
                                            <label class="flex items-center"><input v-model="riskForm.q16" type="radio"
                                                    :value="1" class="mr-2"> Less than 3 years</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">17. How often would you like to review your
                                            portfolio?</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="riskForm.q17" type="radio"
                                                    :value="4" class="mr-2"> Annually</label>
                                            <label class="flex items-center"><input v-model="riskForm.q17" type="radio"
                                                    :value="3" class="mr-2"> Bi-annually</label>
                                            <label class="flex items-center"><input v-model="riskForm.q17" type="radio"
                                                    :value="2" class="mr-2"> Quarterly</label>
                                            <label class="flex items-center"><input v-model="riskForm.q17" type="radio"
                                                    :value="1" class="mr-2"> Monthly</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">18. Are you more focused on:</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="riskForm.q18" type="radio"
                                                    :value="4" class="mr-2"> Wealth growth</label>
                                            <label class="flex items-center"><input v-model="riskForm.q18" type="radio"
                                                    :value="3" class="mr-2"> Balanced growth and preservation</label>
                                            <label class="flex items-center"><input v-model="riskForm.q18" type="radio"
                                                    :value="2" class="mr-2"> Regular income</label>
                                            <label class="flex items-center"><input v-model="riskForm.q18" type="radio"
                                                    :value="1" class="mr-2"> Peace of mind</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">19. If given an option, would you prefer:</p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="riskForm.q19" type="radio"
                                                    :value="4" class="mr-2"> 10% gain with 5% loss</label>
                                            <label class="flex items-center"><input v-model="riskForm.q19" type="radio"
                                                    :value="3" class="mr-2"> 6% gain with 2% loss</label>
                                            <label class="flex items-center"><input v-model="riskForm.q19" type="radio"
                                                    :value="2" class="mr-2"> 3% gain with 0% loss</label>
                                            <label class="flex items-center"><input v-model="riskForm.q19" type="radio"
                                                    :value="1" class="mr-2"> No gain, no risk</label>
                                        </div>
                                    </div>

                                    <div class="border-b pb-4">
                                        <p class="font-semibold mb-2">20. Would you invest in long-term illiquid assets?
                                        </p>
                                        <div class="space-y-1 text-sm">
                                            <label class="flex items-center"><input v-model="riskForm.q20" type="radio"
                                                    :value="4" class="mr-2"> Definitely</label>
                                            <label class="flex items-center"><input v-model="riskForm.q20" type="radio"
                                                    :value="3" class="mr-2"> Likely</label>
                                            <label class="flex items-center"><input v-model="riskForm.q20" type="radio"
                                                    :value="2" class="mr-2"> Hesitant</label>
                                            <label class="flex items-center"><input v-model="riskForm.q20" type="radio"
                                                    :value="1" class="mr-2"> Never</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                    <h3 class="text-lg font-bold text-purple-800 mb-4">📞 Contact Information</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="col-span-1">
                                            <span class="text-sm font-semibold">Full Name:</span>
                                            <input v-model="riskForm.fullName" type="text" readonly
                                                class="w-full border p-2 rounded mt-1 bg-gray-100" />
                                        </div>
                                        <div class="col-span-1">
                                            <span class="text-sm font-semibold">Email Address:</span>
                                            <input v-model="riskForm.email" type="email" readonly
                                                class="w-full border p-2 rounded mt-1 bg-gray-100" />
                                        </div>
                                        <div class="col-span-1">
                                            <span class="text-sm font-semibold">Phone Number: <span
                                                    class="text-red-500">*</span></span>
                                            <input v-model="riskForm.phone" type="tel" required
                                                class="w-full border p-2 rounded mt-1"
                                                placeholder="Enter your phone number" />
                                        </div>
                                    </div>
                                </div>

                                <button @click="submitRiskTolerance"
                                    class="w-full bg-gradient-to-r from-purple-700 to-yellow-500 text-white px-6 py-3 font-bold rounded-lg hover:scale-105 transition-transform">
                                    Submit Risk Assessment
                                </button>

                                <div v-if="showRiskResult"
                                    class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <h4 class="font-bold text-green-800">Your Risk Profile: {{ riskProfile }}</h4>
                                    <p class="text-green-700">Total Score: {{ totalRiskScore }}/80</p>
                                </div>
                            </div>
                        </details>

                        <!-- 4. Money Quiz -->
                        <details class="border rounded">
                            <summary class="cursor-pointer select-none p-4 bg-purple-700 text-white font-semibold">
                                4. Money Quiz - Wealth Score Assessment
                            </summary>
                            <div class="p-6 space-y-4">
                                <p class="text-sm text-gray-600 mb-4">For each question, select the answer that best
                                    describes your financial habits.</p>

                                <!-- Contact Information -->
                                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200 mb-4">
                                    <h3 class="text-lg font-bold text-purple-800 mb-4">📞 Contact Information</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="col-span-1">
                                            <span class="text-sm font-semibold">Name:</span>
                                            <input v-model="userInfo.name" type="text" readonly
                                                class="w-full border p-2 rounded mt-1 bg-gray-100" />
                                        </div>
                                        <div class="col-span-1">
                                            <span class="text-sm font-semibold">Email:</span>
                                            <input v-model="userInfo.email" type="email" readonly
                                                class="w-full border p-2 rounded mt-1 bg-gray-100" />
                                        </div>
                                        <div class="col-span-1">
                                            <span class="text-sm font-semibold">Phone Number: <span
                                                    class="text-red-500">*</span></span>
                                            <input v-model="userInfo.phone" type="tel" required
                                                class="w-full border p-2 rounded mt-1"
                                                placeholder="Enter your phone number" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Goal Setting -->
                                <div class="mb-4">
                                    <h3 class="font-bold text-purple-800">1. Goal Setting</h3>
                                    <label class="block text-gray-700 font-medium mt-2">How often do you set financial
                                        goals?</label>
                                    <select v-model="quizScores.goalSetting1"
                                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                                        <option value="3">A. I set clear short-term and long-term financial goals
                                        </option>
                                        <option value="2">B. I think about financial goals but don't write them down
                                        </option>
                                        <option value="0">C. I don't set financial goals at all</option>
                                    </select>
                                    <label class="block text-gray-700 font-medium mt-2">Do you have a financial plan for
                                        achieving your goals?</label>
                                    <select v-model="quizScores.goalSetting2"
                                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                                        <option value="3">A. Yes, I have a clear plan</option>
                                        <option value="2">B. I have an idea but no clear plan</option>
                                        <option value="0">C. No, I don't have a financial plan</option>
                                    </select>
                                </div>

                                <!-- Investment Planning -->
                                <div class="mb-4">
                                    <h3 class="font-bold text-purple-800">2. Investment Planning</h3>
                                    <label class="block text-gray-700 font-medium mt-2">Have you started
                                        investing?</label>
                                    <select v-model="quizScores.investmentPlanning1"
                                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                                        <option value="3">A. Yes, I actively invest</option>
                                        <option value="2">B. No, but I plan to start soon</option>
                                        <option value="0">C. No, and I'm not interested</option>
                                    </select>
                                    <label class="block text-gray-700 font-medium mt-2">How do you choose your
                                        investments?</label>
                                    <select v-model="quizScores.investmentPlanning2"
                                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                                        <option value="3">A. I research and seek professional advice before investing
                                        </option>
                                        <option value="2">B. I follow trends or invest based on recommendations from
                                            friends</option>
                                        <option value="0">C. I invest randomly or don't invest at all</option>
                                    </select>
                                </div>

                                <!-- Debt Management -->
                                <div class="mb-4">
                                    <h3 class="font-bold text-purple-800">3. Debt Management</h3>
                                    <label class="block text-gray-700 font-medium mt-2">How do you handle debt?</label>
                                    <select v-model="quizScores.debtManagement1"
                                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                                        <option value="3">A. I avoid unnecessary debt and only borrow for good
                                            investments</option>
                                        <option value="2">B. I borrow occasionally but manage repayments well</option>
                                        <option value="0">C. I borrow often and struggle with repayments</option>
                                    </select>
                                    <label class="block text-gray-700 font-medium mt-2">If you had to take a loan, what
                                        would be your reason?</label>
                                    <select v-model="quizScores.debtManagement2"
                                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                                        <option value="3">A. To invest in a business or asset</option>
                                        <option value="2">B. To fund education or skill development</option>
                                        <option value="0">C. To buy personal items like a phone or clothes</option>
                                    </select>
                                </div>

                                <!-- Budget Planning -->
                                <div class="mb-4">
                                    <h3 class="font-bold text-purple-800">4. Budget Planning</h3>
                                    <label class="block text-gray-700 font-medium mt-2">How do you track your income and
                                        expenses?</label>
                                    <select v-model="quizScores.budgetPlanning1"
                                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                                        <option value="3">A. I use a budgeting app or spreadsheet</option>
                                        <option value="2">B. I track my finances mentally but not regularly</option>
                                        <option value="0">C. I don't track my spending at all</option>
                                    </select>
                                    <label class="block text-gray-700 font-medium mt-2">How much of your income do you
                                        save each month?</label>
                                    <select v-model="quizScores.budgetPlanning2"
                                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                                        <option value="3">A. 20% or more</option>
                                        <option value="2">B. 10%-19%</option>
                                        <option value="0">C. Less than 10% or nothing</option>
                                    </select>
                                </div>

                                <!-- Financial Knowledge -->
                                <div class="mb-4">
                                    <h3 class="font-bold text-purple-800">5. Financial Knowledge</h3>
                                    <label class="block text-gray-700 font-medium mt-2">What is the best way to build
                                        wealth over time?</label>
                                    <select v-model="quizScores.financialKnowledge1"
                                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                                        <option value="3">A. Saving and investing wisely</option>
                                        <option value="2">B. Earning more income but not necessarily saving</option>
                                        <option value="0">C. Depending on luck, lottery, or quick schemes</option>
                                    </select>
                                    <label class="block text-gray-700 font-medium mt-2">What is an emergency fund used
                                        for?</label>
                                    <select v-model="quizScores.financialKnowledge2"
                                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                                        <option value="3">A. Unexpected expenses like medical bills and car repairs
                                        </option>
                                        <option value="2">B. Buying things on sale or last-minute purchases</option>
                                        <option value="0">C. Extra cash for parties and fun activities</option>
                                    </select>
                                </div>

                                <button @click="submitMoneyQuiz"
                                    class="w-full bg-gradient-to-r from-purple-700 to-yellow-500 text-white px-6 py-3 font-bold rounded-lg hover:scale-105 transition-transform">
                                    Submit Money Quiz
                                </button>
                            </div>
                        </details>

                    </section>
                </div>
            </Sidebar>
        </div>

        <!-- Money Quiz Result Modal -->
        <div v-if="showQuizModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white p-8 rounded-lg shadow-lg max-w-md mx-auto">
                <h3 class="text-xl font-bold mb-4 text-center">{{ quizResultTitle }}</h3>
                <p class="mb-4 text-center">{{ quizResultMessage }}</p>
                <p class="text-center">Contact us on +254 759 092 412 to grow or maintain your financial position!</p>
                <button @click="showQuizModal = false"
                    class="mt-4 w-full bg-purple-700 text-white px-4 py-2 rounded-lg">Close</button>
                <button @click="contactUs" class="mt-4 w-full bg-yellow-500 text-white px-4 py-2 rounded-lg">Contact
                    Us</button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>