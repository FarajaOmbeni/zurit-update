<template>

    <Head title="Feedback" />
    <Navbar />
    <div class="flex justify-center">
        <div
            class="max-w-3xl mx-4 mb-24 text-sm md:text-lg mt-32 mx-auto p-8 bg-white shadow-xl rounded-lg border-t-4 border-purple-700">
            <h2 class="text-lg md:text-2xl font-bold text-center text-purple-800 mb-6">Customer Feedback</h2>

            <!-- Event Name -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Name of the Event</label>
                <select class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                    <option value="">Property Fundamentals</option>
                    <option value="event2">Investment Strategies</option>
                </select>
            </div>

            <!-- Logistics -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">
                    Please rate the overall organization and logistics of the training sessions (e.g., venue,
                    scheduling,
                    facilities).
                </label>
                <select class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                    <option value="Disorganized">1. Disorganized</option>
                    <option value="Kind of Organized">2. Kind of Organized</option>
                    <option value="Adequate">3. Adequate</option>
                    <option value="Excellent">4. Excellent</option>
                </select>
            </div>

            <!-- Training Clarity -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">
                    How satisfied are you with the clarity and comprehensiveness of the topics covered during the
                    training
                    sessions?
                </label>
                <select class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                    <option value="confusing">1. Confusing</option>
                    <option value="somewhat clear">2. Somewhat Clear</option>
                    <option value="fair">3. Fair</option>
                    <option value="clear">4. Clear</option>
                    <option value="very comprehensive">5. Very Comprehensive</option>
                </select>
            </div>

            <!-- Training Relevance -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">
                    How would you rate the relevance of the topics covered in our training sessions to your financial
                    goals
                    and needs?
                </label>
                <select class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                    <option value="Irrelevant">1. Irrelevant</option>
                    <option value="Somewhat Revlevant">2. Somewhat Revlevant</option>
                    <option value="Moderately Relevant">3. Moderately Relevant</option>
                    <option value="Very Relevant">4. Very Relevant</option>
                    <option value="Extremenly Relevant">5. Extremenly Relevant</option>
                </select>
            </div>

            <!-- Training Recommendation -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">
                    How likely are you to recommend Zurit Consulting's trainings to others based on your experience?
                </label>
                <select class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                    <option value="Unlikely">1. Unlikely</option>
                    <option value="Less Likely">2. Less Likely</option>
                    <option value="Neutral">3. Neutral</option>
                    <option value="Likely">4. Likely</option>
                    <option value="Very Likely">5. Very Likely</option>
                </select>
            </div>
            <!-- Training Recommendation -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">
                    How likely are you to recommend Zurit Consulting's trainings to others based on your experience?
                </label>
                <select class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                    <option value="Unlikely">1. Unlikely</option>
                    <option value="Less Likely">2. Less Likely</option>
                    <option value="Neutral">3. Neutral</option>
                    <option value="Likely">4. Likely</option>
                    <option value="Very Likely">5. Very Likely</option>
                </select>
            </div>

            <!-- Training Attendance -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">
                    How likely are you to attend other Zurit Consulting's trainings based on your experience?
                </label>
                <select class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                    <option value="Unlikely">1. Unlikely</option>
                    <option value="Less Likely">2. Less Likely</option>
                    <option value="Neutral">3. Neutral</option>
                    <option value="Likely">4. Likely</option>
                    <option value="Very Likely">5. Very Likely</option>
                </select>
            </div>

            <!-- Training Value -->
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">
                    Please rate the value for money of the financial training and services provided by Zurit Consulting.
                </label>
                <select class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                    <option value="No Value">1. No Value</option>
                    <option value="Poor Value">2. Poor Value</option>
                    <option value="Fair Value">3. Fair Value</option>
                    <option value="Good Value">4. Good Value</option>
                    <option value="Excellent Value">5. Excellent Value</option>
                </select>
            </div>

            <!-- Textarea Questions -->
            <div v-for="(question, index) in openQuestions" :key="index" class="mb-4">
                <label class="block text-gray-700 font-medium">{{ question.label }}</label>
                <textarea rows="3" class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500"
                    placeholder="Write your response..."></textarea>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 text-center">
                <button class="w-full bg-gradient-to-r from-purple-700 to-yellow-500 text-white px-6 py-3 font-bold rounded-lg 
                     transition-transform transform hover:scale-105">
                    Submit
                </button>
            </div>
        </div>
    </div>
    <Footer />
</template>
 
<script setup>
import { Head } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';

const openQuestions = [
    { label: "What aspects of our training sessions did you find most valuable?" },
    { label: "Is there anything specific that you feel could be improved about our training programs?" },
    { label: "Are there any additional topics or areas of interest you would like to see covered in future training sessions?" },
    { label: "Who was your favorite trainer/speaker?" }
];
</script>