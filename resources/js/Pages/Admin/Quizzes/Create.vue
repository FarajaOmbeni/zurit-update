<template>
    <AdminSidebar title="Create Quiz">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-xl font-semibold text-gray-900">Create Quiz</h1>
                    <p class="mt-2 text-sm text-gray-700">Add a new quiz for a sub-course</p>
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                    <button
                        type="button"
                        @click="$inertia.visit(route('admin.quizzes.index'))"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Back to Quizzes
                    </button>
                </div>
            </div>

            <form @submit.prevent="submit" class="mt-8 space-y-8">
                <!-- Quiz Basic Information -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Quiz Information</h3>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Course Selection -->
                        <div>
                            <label for="subcourse_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Select Sub-Course *
                            </label>
                            <select
                                id="subcourse_id"
                                v-model="form.subcourse_id"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                required
                            >
                                <option value="">Choose a sub-course</option>
                                <option
                                    v-for="course in subCourses"
                                    :key="course.id"
                                    :value="course.id"
                                >
                                    {{ course.full_title }}
                                </option>
                            </select>
                            <p v-if="form.errors.subcourse_id" class="mt-1 text-sm text-red-600">
                                {{ form.errors.subcourse_id }}
                            </p>
                        </div>

                        <!-- Quiz Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Quiz Title *
                            </label>
                            <input
                                type="text"
                                id="title"
                                v-model="form.title"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Enter quiz title"
                                required
                            />
                            <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <!-- Quiz Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Quiz Description
                            </label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Enter quiz description (optional)"
                            ></textarea>
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                {{ form.errors.description }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Questions Section -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Quiz Questions</h3>
                        <button
                            type="button"
                            @click="addQuestion"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Question
                        </button>
                    </div>

                    <div v-if="form.questions.length === 0" class="text-center py-8">
                        <div class="text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No questions added yet</h3>
                            <p class="text-sm text-gray-500">Click "Add Question" to start building your quiz.</p>
                        </div>
                    </div>

                    <div v-else class="space-y-6">
                        <div
                            v-for="(question, questionIndex) in form.questions"
                            :key="questionIndex"
                            class="border border-gray-200 rounded-lg p-6 bg-gray-50"
                        >
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-md font-medium text-gray-900">
                                    Question {{ questionIndex + 1 }}
                                </h4>
                                <button
                                    type="button"
                                    @click="removeQuestion(questionIndex)"
                                    class="text-red-600 hover:text-red-800"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Question Text -->
                            <div class="mb-4">
                                <label :for="'question-' + questionIndex" class="block text-sm font-medium text-gray-700 mb-2">
                                    Question Text *
                                </label>
                                <textarea
                                    :id="'question-' + questionIndex"
                                    v-model="question.question"
                                    rows="3"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Enter your question"
                                    required
                                ></textarea>
                                <p v-if="form.errors[`questions.${questionIndex}.question`]" class="mt-1 text-sm text-red-600">
                                    {{ form.errors[`questions.${questionIndex}.question`] }}
                                </p>
                            </div>

                            <!-- Choices -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Answer Choices *
                                    </label>
                                    <button
                                        type="button"
                                        @click="addChoice(questionIndex)"
                                        class="text-sm text-indigo-600 hover:text-indigo-800"
                                    >
                                        + Add Choice
                                    </button>
                                </div>

                                <div class="space-y-2">
                                    <div
                                        v-for="(choice, choiceIndex) in question.choices"
                                        :key="choiceIndex"
                                        class="flex items-center space-x-3"
                                    >
                                        <input
                                            type="radio"
                                            :name="'correct-' + questionIndex"
                                            :value="choiceIndex"
                                            v-model="question.correct_choice"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        />
                                        <input
                                            type="text"
                                            v-model="choice.choice"
                                            class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                            placeholder="Enter choice text"
                                            required
                                        />
                                        <button
                                            v-if="question.choices.length > 2"
                                            type="button"
                                            @click="removeChoice(questionIndex, choiceIndex)"
                                            class="text-red-600 hover:text-red-800"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <p v-if="form.errors[`questions.${questionIndex}.choices`]" class="mt-1 text-sm text-red-600">
                                    {{ form.errors[`questions.${questionIndex}.choices`] }}
                                </p>
                                <p v-if="form.errors[`questions.${questionIndex}.correct_choice`]" class="mt-1 text-sm text-red-600">
                                    {{ form.errors[`questions.${questionIndex}.correct_choice`] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3">
                    <button
                        type="button"
                        @click="$inertia.visit(route('admin.quizzes.index'))"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                    >
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ form.processing ? 'Creating Quiz...' : 'Create Quiz' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminSidebar>
</template>

<script setup>
import AdminSidebar from '@/Components/AdminSidebar.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    subCourses: Array,
});

const form = useForm({
    subcourse_id: '',
    title: '',
    description: '',
    questions: []
});

const addQuestion = () => {
    form.questions.push({
        question: '',
        choices: [
            { choice: '' },
            { choice: '' }
        ],
        correct_choice: 0
    });
};

const removeQuestion = (index) => {
    form.questions.splice(index, 1);
};

const addChoice = (questionIndex) => {
    form.questions[questionIndex].choices.push({ choice: '' });
};

const removeChoice = (questionIndex, choiceIndex) => {
    form.questions[questionIndex].choices.splice(choiceIndex, 1);
    // Adjust correct_choice if necessary
    if (form.questions[questionIndex].correct_choice >= choiceIndex) {
        form.questions[questionIndex].correct_choice = Math.max(0, form.questions[questionIndex].correct_choice - 1);
    }
};

const submit = () => {
    form.post(route('admin.quizzes.store'));
};
</script> 