<script setup>
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DashboardBackButton from '@/Components/Shared/DashboardBackButton.vue';

const scores = ref({
    goalSetting1: 0,
    goalSetting2: 0,
    investmentPlanning1: 0,
    investmentPlanning2: 0,
    debtManagement1: 0,
    debtManagement2: 0,
    budgetPlanning1: 0,
    budgetPlanning2: 0,
    financialKnowledge1: 0,
    financialKnowledge2: 0,
});

const userInfo = ref({
    name: '',
    email: '',
    phone: ''
});

const showModal = ref(false);
const totalScore = computed(() => {
    return Object.values(scores.value).reduce((acc, score) => acc + parseInt(score), 0);
});

const resultTitle = computed(() => {
    if (totalScore.value >= 21) return '💎 Wealth Master';
    if (totalScore.value >= 11) return '📈 Growing Investor';
    return '🚦 Financial Starter';
});

const resultMessage = computed(() => {
    if (totalScore.value >= 21) {
        return 'You have excellent financial habits and are on track to achieving financial security and success. Keep up the great work!';
    } else if (totalScore.value >= 11) {
        return 'You are making good financial decisions but need to fine-tune your savings, investments, and budgeting strategies to build more wealth.';
    } else {
        return 'You need to improve your financial planning, budgeting, and investment strategies. Start by setting clear financial goals and learning more about managing money.';
    }
});

function calculateScore() {
    showModal.value = true;
}

function contactUs() {
    const form = useForm({
        name: userInfo.value.name,
        email: userInfo.value.email,
        phone: userInfo.value.phone,
        message: resultMessage.value,
    });

    form.post(route('submit.quiz'), {
        onSuccess: () => {
            showModal.value = false;
            alert('Your information has been sent successfully! We will get back to you soon.');
        }
    });
}
</script>

<template>

    <Head title="Money Quiz" />
    <AuthenticatedLayout>
        <DashboardBackButton />
        <div class="flex justify-center">
            <div
                class="max-w-3xl mx-4 mb-24 text-sm md:text-lg mt-32 mx-auto p-8 bg-white shadow-xl rounded-lg border-t-4 border-purple-700">
                <h2 class="text-lg md:text-2xl font-bold text-center text-purple-800 mb-6">Wealth Score Questionnaire 💰
                </h2>
                <p class="mb-6 text-center">Instructions: For each question, select the answer that best describes your
                    financial habits. At the end, add up your score to determine your Wealth Score Level.</p>

                <!-- User Information -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mt-2">Name:</label>
                    <input required v-model="userInfo.name" type="text"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500"
                        placeholder="Enter your name" />

                    <label class="block text-gray-700 font-medium mt-2">Email:</label>
                    <input required v-model="userInfo.email" type="email"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500"
                        placeholder="Enter your email" />

                    <label class="block text-gray-700 font-medium mt-2">Phone Number:</label>
                    <input required v-model="userInfo.phone" type="phone"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500"
                        placeholder="Enter your phone number" />
                </div>

                <!-- Goal Setting -->
                <div class="mb-4">
                    <h3 class="font-bold text-purple-800">1. Goal Setting</h3>
                    <label class="block text-gray-700 font-medium mt-2">How often do you set financial goals?</label>
                    <select v-model="scores.goalSetting1"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                        <option value="3">A. I set clear short-term and long-term financial goals</option>
                        <option value="2">B. I think about financial goals but don't write them down</option>
                        <option value="0">C. I don't set financial goals at all</option>
                    </select>

                    <label class="block text-gray-700 font-medium mt-2">Do you have a financial plan for achieving your
                        goals?</label>
                    <select v-model="scores.goalSetting2"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                        <option value="3">A. Yes, I have a clear plan</option>
                        <option value="2">B. I have an idea but no clear plan</option>
                        <option value="0">C. No, I don't have a financial plan</option>
                    </select>
                </div>

                <!-- Investment Planning -->
                <div class="mb-4">
                    <h3 class="font-bold text-purple-800">2. Investment Planning</h3>
                    <label class="block text-gray-700 font-medium mt-2">Have you started investing?</label>
                    <select v-model="scores.investmentPlanning1"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                        <option value="3">A. Yes, I actively invest</option>
                        <option value="2">B. No, but I plan to start soon</option>
                        <option value="0">C. No, and I'm not interested</option>
                    </select>

                    <label class="block text-gray-700 font-medium mt-2">How do you choose your investments?</label>
                    <select v-model="scores.investmentPlanning2"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                        <option value="3">A. I research and seek professional advice before investing</option>
                        <option value="2">B. I follow trends or invest based on recommendations from friends</option>
                        <option value="0">C. I invest randomly or don't invest at all</option>
                    </select>
                </div>

                <!-- Debt Management -->
                <div class="mb-4">
                    <h3 class="font-bold text-purple-800">3. Debt Management</h3>
                    <label class="block text-gray-700 font-medium mt-2">How do you handle debt?</label>
                    <select v-model="scores.debtManagement1"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                        <option value="3">A. I avoid unnecessary debt and only borrow for good investments</option>
                        <option value="2">B. I borrow occasionally but manage repayments well</option>
                        <option value="0">C. I borrow often and struggle with repayments</option>
                    </select>

                    <label class="block text-gray-700 font-medium mt-2">If you had to take a loan, what would be your
                        reason?</label>
                    <select v-model="scores.debtManagement2"
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
                    <select v-model="scores.budgetPlanning1"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                        <option value="3">A. I use a budgeting app or spreadsheet</option>
                        <option value="2">B. I track my finances mentally but not regularly</option>
                        <option value="0">C. I don't track my spending at all</option>
                    </select>

                    <label class="block text-gray-700 font-medium mt-2">How much of your income do you save each
                        month?</label>
                    <select v-model="scores.budgetPlanning2"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                        <option value="3">A. 20% or more</option>
                        <option value="2">B. 10%-19%</option>
                        <option value="0">C. Less than 10% or nothing</option>
                    </select>
                </div>

                <!-- Financial Knowledge -->
                <div class="mb-4">
                    <h3 class="font-bold text-purple-800">5. Financial Knowledge</h3>
                    <label class="block text-gray-700 font-medium mt-2">What is the best way to build wealth over
                        time?</label>
                    <select v-model="scores.financialKnowledge1"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                        <option value="3">A. Saving and investing wisely</option>
                        <option value="2">B. Earning more income but not necessarily saving</option>
                        <option value="0">C. Depending on luck, lottery, or quick schemes</option>
                    </select>

                    <label class="block text-gray-700 font-medium mt-2">What is an emergency fund used for?</label>
                    <select v-model="scores.financialKnowledge2"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                        <option value="3">A. Unexpected expenses like medical bills and car repairs</option>
                        <option value="2">B. Buying things on sale or last-minute purchases</option>
                        <option value="0">C. Extra cash for parties and fun activities</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 text-center">
                    <button @click="calculateScore" class="w-full bg-gradient-to-r from-purple-700 to-yellow-500 text-white px-6 py-3 font-bold rounded-lg 
                     transition-transform transform hover:scale-105">
                        Submit
                    </button>
                </div>
            </div>
        </div>

        <!-- Result Modal -->
        <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white p-8 rounded-lg shadow-lg max-w-md mx-auto">
                <h3 class="text-xl font-bold mb-4 text-center">{{ resultTitle }}</h3>
                <p class="mb-4 text-center">{{ resultMessage }}</p>
                <p class="text-center">Contact us on +254 759 092 412 to grow or maintain your financial position!</p>
                <button @click="showModal = false" class="mt-4 w-full bg-purple-700 text-white px-4 py-2 rounded-lg">
                    Close
                </button>
                <button @click="contactUs" class="mt-4 w-full bg-yellow-500 text-white px-4 py-2 rounded-lg">
                    Contact Us
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>