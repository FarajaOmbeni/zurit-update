<template>
    <Head title="Quiz Results" />
    <Sidebar title="Quiz Results">
        <div class="bg-gray-50 min-h-screen">
            <!-- Result Header -->
            <div class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <nav class="flex" aria-label="Breadcrumb">
                                <ol class="flex items-center space-x-4">
                                    <li>
                                        <div>
                                            <button
                                                @click="$inertia.visit(route('elearning.courses'))"
                                                class="text-gray-400 hover:text-gray-500"
                                            >
                                                <svg class="flex-shrink-0 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="flex items-center">
                                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="ml-4 text-sm font-medium text-gray-500">
                                                {{ course.parent?.title }} - {{ course.title }}
                                            </span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="flex items-center">
                                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="ml-4 text-sm font-medium text-gray-900">Results</span>
                                        </div>
                                    </li>
                                </ol>
                            </nav>
                            <h1 class="mt-4 text-3xl font-bold text-gray-900">Quiz Results</h1>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Score Summary -->
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 mb-8">
                    <div class="text-center">
                        <div class="mb-6">
                            <div class="text-6xl font-bold mb-2"
                                :class="{
                                    'text-green-600': passed,
                                    'text-red-600': !passed
                                }"
                            >
                                {{ percentage }}%
                            </div>
                            <div class="text-xl text-gray-600 mb-4">
                                {{ score }} out of {{ totalQuestions }} correct
                            </div>
                            
                            <!-- Pass/Fail Status -->
                            <div class="mb-6">
                                <div class="inline-flex items-center px-6 py-3 rounded-full text-lg font-semibold"
                                    :class="{
                                        'bg-green-100 text-green-800 border-2 border-green-300': passed,
                                        'bg-red-100 text-red-800 border-2 border-red-300': !passed
                                    }"
                                >
                                    <svg v-if="passed" class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <svg v-else class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    {{ passed ? 'PASSED' : 'FAILED' }}
                                </div>
                            </div>
                            
                            <!-- Pass Mark Info -->
                            <div class="text-sm text-gray-500 mb-6">
                                Pass mark: 70% (You need 70% to pass)
                            </div>
                        </div>

                        <div class="mb-8">
                            <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium"
                                :class="{
                                    'bg-green-100 text-green-800': passed,
                                    'bg-red-100 text-red-800': !passed
                                }"
                            >
                                <svg v-if="passed" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ getPerformanceMessage() }}
                            </div>
                        </div>

                        <div class="flex justify-center space-x-4">
                            <button
                                @click="retakeQuiz"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Retake Quiz
                            </button>
                            <button
                                @click="$inertia.visit(route('elearning.course', { course: course.id }))"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                                Back to Course
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Detailed Results -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Detailed Results</h2>
                    
                    <div class="space-y-6">
                        <div
                            v-for="(result, index) in results"
                            :key="index"
                            class="border border-gray-200 rounded-lg p-6"
                            :class="{
                                'bg-green-50 border-green-200': result.is_correct,
                                'bg-red-50 border-red-200': !result.is_correct
                            }"
                        >
                            <div class="flex items-start justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">
                                    Question {{ index + 1 }}
                                </h3>
                                <div class="flex items-center">
                                    <svg v-if="result.is_correct" class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <svg v-else class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                            </div>
                            
                            <p class="text-gray-700 mb-4">{{ result.question }}</p>
                            
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-500 w-24">Your Answer:</span>
                                    <span class="text-sm text-gray-900">{{ result.user_answer }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-500 w-24">Correct Answer:</span>
                                    <span class="text-sm text-gray-900">{{ result.correct_answer }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Sidebar>
</template>

<script setup>
import Sidebar from "@/Components/Sidebar.vue";

const props = defineProps({
    score: Number,
    totalQuestions: Number,
    percentage: Number,
    passed: Boolean,
    results: Array,
    course: Object,
});

const getPerformanceMessage = () => {
    if (props.passed) {
        return 'Congratulations! You passed the quiz!';
    } else {
        return 'Keep studying and try again to pass!';
    }
};

const retakeQuiz = () => {
    // Navigate to quiz page for retaking
    window.location.href = route('elearning.quiz', { course: props.course.id });
};
</script> 