<template>

    <Head title="Feedback" />
    <Navbar />
    <div class="flex justify-center">
        <div
            class="max-w-3xl mx-4 mb-24 text-sm md:text-lg mt-32 mx-auto p-8 bg-white shadow-xl rounded-lg border-t-4 border-purple-700">
            <h2 class="text-lg md:text-2xl font-bold text-center text-purple-800 mb-6">Customer Feedback</h2>

            <form @submit.prevent="submit">
                <!-- Event Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Name of the Event</label>
                    <select v-model="form.eventName"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                        <option value="Select Event" hidden>Select Event</option>
                        <option value="Property Fundamentals">Property Fundamentals</option>
                        <option value="Investment Strategies">Investment Strategies</option>
                        <option value="Alternative Investments">Alternative Investments</option>
                        <option value="Retirement Planning">Retirement Planning</option>
                        <option value="Business Planning">Business Planning</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <!-- Logistics -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">
                        Please rate the overall organization and logistics of the training sessions (e.g., venue,
                        scheduling,
                        facilities).
                    </label>
                    <select v-model="form.logisticsRating"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                        <option value="Select Logistics Rating" hidden>Select Logistics Rating</option>
                        <option value="Disorganized - 1">1. Disorganized</option>
                        <option value="Kind of Organized - 2">2. Kind of Organized</option>
                        <option value="Adequate - 3">3. Adequate</option>
                        <option value="Excellent - 4">4. Excellent</option>
                    </select>
                </div>

                <!-- Training Clarity -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">
                        How satisfied are you with the clarity and comprehensiveness of the topics covered during the
                        training
                        sessions?
                    </label>
                    <select v-model="form.clarityRating"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                        <option value="Select Clarity Rating" hidden>Select Clarity Rating</option>
                        <option value="confusing - 1">1. Confusing</option>
                        <option value="somewhat clear - 2">2. Somewhat Clear</option>
                        <option value="fair - 3">3. Fair</option>
                        <option value="clear - 4">4. Clear</option>
                        <option value="very comprehensive - 5">5. Very Comprehensive</option>
                    </select>
                </div>

                <!-- Training Relevance -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">
                        How would you rate the relevance of the topics covered in our training sessions to your
                        financial
                        goals
                        and needs?
                    </label>
                    <select v-model="form.relevanceRating"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                        <option value="Select Relevance Rating" hidden>Select Relevance Rating</option>
                        <option value="Irrelevant - 1">1. Irrelevant</option>
                        <option value="Somewhat Relevant - 2">2. Somewhat Relevant</option>
                        <option value="Moderately Relevant - 3">3. Moderately Relevant</option>
                        <option value="Very Relevant - 4">4. Very Relevant</option>
                        <option value="Extremely Relevant - 5">5. Extremely Relevant</option>
                    </select>
                </div>

                <!-- Training Recommendation -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">
                        How likely are you to recommend Zurit Consulting's trainings to others based on your experience?
                    </label>
                    <select v-model="form.recommendationLikelihood"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                        <option value="Select Recommendation Likelihood" hidden>Select Recommendation Likelihood</option>
                        <option value="Unlikely - 1">1. Unlikely</option>
                        <option value="Less Likely - 2">2. Less Likely</option>
                        <option value="Neutral - 3">3. Neutral</option>
                        <option value="Likely - 4">4. Likely</option>
                        <option value="Very Likely - 5">5. Very Likely</option>
                    </select>
                </div>
                <!-- Training Attendance -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">
                        How likely are you to attend other Zurit Consulting's trainings based on your experience?
                    </label>
                    <select v-model="form.attendanceLikelihood"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                        <option value="Select Attendance Likelihood" hidden>Select Attendance Likelihood</option>
                        <option value="Unlikely - 1">1. Unlikely</option>
                        <option value="Less Likely - 2">2. Less Likely</option>
                        <option value="Neutral - 3">3. Neutral</option>
                        <option value="Likely - 4">4. Likely</option>
                        <option value="Very Likely - 5">5. Very Likely</option>
                    </select>
                </div>

                <!-- Training Value -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">
                        Please rate the value for money of the financial training and services provided by Zurit
                        Consulting.
                    </label>
                    <select v-model="form.valueForMoney"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500">
                        <option value="Select Value for Money" hidden>Select Value for Money</option>
                        <option value="No Value - 1">1. No Value</option>
                        <option value="Poor Value - 2">2. Poor Value</option>
                        <option value="Fair Value - 3">3. Fair Value</option>
                        <option value="Good Value - 4">4. Good Value</option>
                        <option value="Excellent Value - 5">5. Excellent Value</option>
                    </select>
                </div>

                <!-- Textarea Questions -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">What aspects of our training sessions did you find
                        most
                        valuable?</label>
                    <textarea v-model="form.mostValuable" name="most_valuable" rows="3"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500"
                        placeholder="Write your response..."></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Is there anything specific that you feel could be
                        improved about our training programs?</label>
                    <textarea v-model="form.areaOfImprovement" name="area_of_improvement" rows="3"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500"
                        placeholder="Write your response..."></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Are there any additional topics or areas of interest
                        you
                        would like to see covered in future training sessions?</label>
                    <textarea v-model="form.topicSuggestion" name="topic_suggestion" rows="3"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500"
                        placeholder="Write your response..."></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Who was your favorite trainer/speaker?</label>
                    <textarea v-model="form.favoriteSpeaker" name="favorite_speaker" rows="3"
                        class="w-full px-4 py-2 mt-1 border rounded-md focus:ring-2 focus:ring-purple-500"
                        placeholder="Write your response..."></textarea>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 text-center">
                    <button :disabled="form.processing" type="submit" class="w-full bg-gradient-to-r from-purple-700 to-yellow-500 text-white px-6 py-3 font-bold rounded-lg 
                     transition-transform transform hover:scale-105"
                     :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                     >
                        {{ form.processing ? 'Submitting...' : 'Submit' }}
                    </button>
                    <!-- Success Message -->
                    <p v-if="showSuccess" class="text-green-600">
                        Feedback sent successfully! Thank you for your feedback!
                    </p>
                </div>
            </form>
        </div>
    </div>
    <Footer />
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import { ref, nextTick } from 'vue'

// Save scroll position before submitting
const saveScrollPosition = () => {
    sessionStorage.setItem('scrollPosition', window.scrollY);
}

// Restore scroll position after the form is submitted or page reloads
const restoreScrollPosition = () => {
    const savedPosition = sessionStorage.getItem('scrollPosition');
    if (savedPosition) {
        window.scrollTo(0, savedPosition);
        sessionStorage.removeItem('scrollPosition');
    }
}

const showSuccess = ref(false);

const form = useForm({
    eventName: 'Select Event',
    logisticsRating: 'Select Logistics Rating',
    clarityRating: 'Select Clarity Rating',
    relevanceRating: 'Select Relevance Rating',
    recommendationLikelihood: 'Select Recommendation Likelihood',
    attendanceLikelihood: 'Select Attendance Likelihood',
    valueForMoney: 'Select Value for Money',
    mostValuable: '',
    areaOfImprovement: '',
    topicSuggestion: '',
    favoriteSpeaker: ''
})

const submit = () => {
    saveScrollPosition();

    form.post(route('send.feedback'), {
        onSuccess: () => {
            form.reset()
            showSuccess.value = true;
            nextTick(() => {
                setTimeout(() => {
                    showSuccess.value = false;
                }, 5000);
            });

            restoreScrollPosition();  // Restore scroll position after submission
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors)
                .flat()
                .join(' ');

            openAlert('danger', errorMessages, 5000);
        }
    })
}

// Restore scroll position on page load
restoreScrollPosition();
</script>