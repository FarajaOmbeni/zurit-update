<template>

    <Head title="Quiz" />
    <ElearningSidebar title="Course Quiz">
        <div class="bg-gray-50 min-h-screen">
            <!-- Quiz Header -->
            <div class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <nav class="flex" aria-label="Breadcrumb">
                                <ol class="flex items-center space-x-4">
                                    <li>
                                        <div>
                                            <button @click="$inertia.visit(route('elearning.courses'))"
                                                class="text-gray-400 hover:text-gray-500">
                                                <svg class="flex-shrink-0 h-5 w-5" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="flex items-center">
                                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span class="ml-4 text-sm font-medium text-gray-500">
                                                {{ course.parent?.title }} - {{ course.title }}
                                            </span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="flex items-center">
                                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span class="ml-4 text-sm font-medium text-gray-900">Quiz</span>
                                        </div>
                                    </li>
                                </ol>
                            </nav>
                            <h1 class="mt-4 text-3xl font-bold text-gray-900">{{ quiz.title }}</h1>
                            <p v-if="quiz.description" class="mt-2 text-gray-600">{{ quiz.description }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">Question {{ currentQuestionIndex + 1 }} of {{
                                quiz.questions.length }}</div>
                            <div class="text-2xl font-bold text-indigo-600">{{ Math.round((currentQuestionIndex + 1) /
                                quiz.questions.length * 100) }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quiz Content -->
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Completed Quiz Status -->
                <div v-if="latestAttempt && showAttemptStatus"
                    class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 mb-8">
                    <div class="text-center">
                        <div class="mb-6">
                            <div class="text-4xl font-bold mb-2" :class="{
                                'text-green-600': latestAttempt.passed,
                                'text-red-600': !latestAttempt.passed
                            }">
                                {{ latestAttempt.percentage }}%
                            </div>
                            <div class="text-lg text-gray-600 mb-4">
                                {{ latestAttempt.score }} out of {{ latestAttempt.total_questions }} correct
                            </div>

                            <!-- Pass/Fail Status -->
                            <div class="mb-6">
                                <div class="inline-flex items-center px-6 py-3 rounded-full text-lg font-semibold"
                                    :class="{
                                        'bg-green-100 text-green-800 border-2 border-green-300': latestAttempt.passed,
                                        'bg-red-100 text-red-800 border-2 border-red-300': !latestAttempt.passed
                                    }">
                                    <svg v-if="latestAttempt.passed" class="w-6 h-6 mr-3" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <svg v-else class="w-6 h-6 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    {{ latestAttempt.passed ? 'PASSED' : 'FAILED' }}
                                </div>
                            </div>

                            <div class="text-sm text-gray-500 mb-6">
                                Completed on {{ new Date(latestAttempt.completed_at).toLocaleDateString() }}
                            </div>
                        </div>

                        <div class="flex justify-center space-x-4">
                            <button @click="startNewAttempt"
                                class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reattempt Quiz
                            </button>
                            <button @click="$inertia.visit(route('elearning.courses'))"
                                class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                                Back to Course
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="currentQuestion && (!latestAttempt || !showAttemptStatus)"
                    class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                    <!-- Question -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">
                            Question {{ currentQuestionIndex + 1 }}
                        </h2>
                        <p class="text-lg text-gray-700 leading-relaxed">{{ currentQuestion.question }}</p>
                    </div>

                    <!-- Choices -->
                    <div class="space-y-4">
                        <div v-for="choice in currentQuestion.choices" :key="choice.id" class="relative">
                            <input :id="'choice-' + choice.id" type="radio" :name="'question-' + currentQuestion.id"
                                :value="choice.id" v-model="answers[currentQuestion.id]" class="sr-only" />
                            <label :for="'choice-' + choice.id"
                                class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-300 transition-colors"
                                :class="{
                                    'border-indigo-500 bg-indigo-50': answers[currentQuestion.id] === choice.id,
                                    'border-gray-200 bg-white': answers[currentQuestion.id] !== choice.id
                                }">
                                <div class="flex items-center justify-center w-6 h-6 rounded-full border-2 mr-4" :class="{
                                    'border-indigo-500 bg-indigo-500': answers[currentQuestion.id] === choice.id,
                                    'border-gray-300': answers[currentQuestion.id] !== choice.id
                                }">
                                    <div v-if="answers[currentQuestion.id] === choice.id"
                                        class="w-2 h-2 bg-white rounded-full"></div>
                                </div>
                                <span class="text-gray-900">{{ choice.choice }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                        <button v-if="currentQuestionIndex > 0" @click="previousQuestion"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Previous
                        </button>
                        <div v-else></div>

                        <div class="flex space-x-3">
                            <button v-if="currentQuestionIndex < quiz.questions.length - 1" @click="nextQuestion"
                                :disabled="!answers[currentQuestion.id]"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                Next
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <button v-else @click="submitQuiz"
                                :disabled="!answers[currentQuestion.id] || form.processing"
                                class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                {{ form.processing ? 'Submitting...' : 'Submit Quiz' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div v-if="(!latestAttempt || !showAttemptStatus)" class="mt-8">
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                        <span>Progress</span>
                        <span>{{ answeredQuestions }} of {{ quiz.questions.length }} answered</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                            :style="{ width: (answeredQuestions / quiz.questions.length) * 100 + '%' }"></div>
                    </div>
                </div>
            </div>
        </div>
    </ElearningSidebar>
</template>

<script setup>
import ElearningSidebar from "@/Components/ElearningSidebar.vue";
import { useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    quiz: Object,
    course: Object,
    latestAttempt: Object,
});

const currentQuestionIndex = ref(0);
const answers = ref({});
const showAttemptStatus = ref(true);

const form = useForm({
    answers: {}
});

const currentQuestion = computed(() => {
    return props.quiz.questions[currentQuestionIndex.value];
});

const answeredQuestions = computed(() => {
    return Object.keys(answers.value).length;
});

const nextQuestion = () => {
    if (currentQuestionIndex.value < props.quiz.questions.length - 1) {
        currentQuestionIndex.value++;
    }
};

const previousQuestion = () => {
    if (currentQuestionIndex.value > 0) {
        currentQuestionIndex.value--;
    }
};

const submitQuiz = () => {
    form.answers = answers.value;
    form.post(route('elearning.quiz.submit', { course: props.course.id }));
};

const startNewAttempt = () => {
    // Hide the attempt status and show fresh quiz
    showAttemptStatus.value = false;
    currentQuestionIndex.value = 0;
    answers.value = {};
    form.reset();
};
</script>