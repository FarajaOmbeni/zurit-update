<template>
    <Head title="Courses" />
    <ElearningSidebar title="All Courses">
        <div class="bg-gray-50 min-h-screen">
            <!-- Page Header -->
            <div
                class="relative text-white pt-28 pb-10 md:pt-56 md:pb-16 overflow-hidden"
                style="
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                "
            >
                <div class="absolute inset-0 bg-black bg-opacity-10 z-10"></div>

                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20">
                    <div class="text-center mb-8 md:mb-12">
                        <h1
                            class="text-3xl sm:text-5xl md:text-6xl font-extrabold mb-3 sm:mb-4 tracking-tight"
                        >
                            All Courses
                        </h1>
                        <p
                            class="text-base sm:text-xl opacity-90 max-w-2xl mx-auto leading-relaxed"
                        >
                            Explore our comprehensive collection of financial
                            literacy courses
                        </p>
                    </div>

                    <!-- Course Stats -->
                    <div class="flex justify-center gap-6 md:gap-12 flex-wrap">
                        <div class="text-center">
                            <div
                                class="text-3xl sm:text-4xl md:text-5xl font-bold mb-1 sm:mb-2 text-yellow-200"
                            >
                                {{ courses.length }}
                            </div>
                            <div
                                class="text-xs sm:text-sm opacity-80 uppercase tracking-wider"
                            >
                                Main Courses
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Filters -->
            <div class="bg-white py-4 sm:py-8 border-b border-gray-200 sticky top-0 z-50">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex gap-3 sm:gap-4 justify-center flex-wrap">
                        <button
                            class="px-4 py-2 sm:px-6 sm:py-3 border-2 border-indigo-500 bg-indigo-500 text-white rounded-full text-sm sm:text-base font-semibold cursor-pointer transition-all duration-200 hover:bg-indigo-600 hover:border-indigo-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-white"
                        >
                            All Courses
                        </button>
                    </div>
                </div>
            </div>

            <!-- Course Content -->
            <div class="py-10 sm:py-16 bg-gray-50 min-h-[60vh]">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <!-- Empty State -->
                    <div
                        v-if="courses.length === 0"
                        class="text-center py-12 sm:py-16 px-6 sm:px-8 max-w-lg mx-auto"
                    >
                        <div
                            class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-6 sm:mb-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center text-white"
                        >
                            <svg
                                class="w-8 h-8 sm:w-10 sm:h-10 stroke-2"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                <path
                                    d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"
                                />
                                <path d="M8 7h8" />
                                <path d="M8 11h8" />
                                <path d="M8 15h6" />
                            </svg>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-3 sm:mb-4">
                            No courses available yet
                        </h3>
                        <p class="text-gray-600 leading-relaxed mb-6 sm:mb-8 text-sm sm:text-base">
                            We're working hard to bring you amazing financial
                            literacy courses. Check back soon for updates!
                        </p>
                        <button
                            class="inline-flex items-center gap-2 px-5 py-3 text-sm font-semibold rounded-lg text-white bg-gradient-to-r from-indigo-500 to-purple-600 shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-white"
                        >
                            Get Notified When Available
                        </button>
                    </div>

                    <!-- Course Grid -->
                    <div
                        v-else
                        class="space-y-6 sm:space-y-8"
                    >
                        <div
                            v-for="mainCourse in courses"
                            :key="mainCourse.id"
                            class="bg-white rounded-2xl overflow-hidden transition-all duration-300 shadow-sm border border-gray-200 hover:shadow-lg hover:-translate-y-0.5"
                        >
                            <!-- Main Course Header -->
                            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-5 sm:p-6 md:p-8">
                                <div class="flex items-start sm:items-center justify-between gap-4">
                                    <div class="flex items-start sm:items-center space-x-4">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm ring-1 ring-white/20">
                                            <svg
                                                class="w-5 h-5 sm:w-6 sm:h-6 text-white stroke-2"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"
                                                />
                                                <path
                                                    d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"
                                                />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex flex-wrap items-center gap-3 sm:gap-4 mb-2">
                                                <h2 class="text-xl sm:text-2xl font-bold text-white">
                                                    {{ mainCourse.title }}
                                                </h2>
                                                <div v-if="mainCourse.is_completed" class="flex items-center gap-2 bg-green-500 bg-opacity-20 px-2.5 py-0.5 rounded-full">
                                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-green-200" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path d="M9 12l2 2 4-4" />
                                                        <path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9s4.03-9 9-9c1.51 0 2.93.37 4.18 1.03" />
                                                    </svg>
                                                    <span class="text-green-200 text-xs sm:text-sm font-medium">Completed</span>
                                                </div>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-white text-xs sm:text-sm opacity-90 mb-3">
                                                <span class="flex items-center space-x-1">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path d="M9 12l2 2 4-4" />
                                                        <path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9s4.03-9 9-9c1.51 0 2.93.37 4.18 1.03" />
                                                    </svg>
                                                    <span>{{ mainCourse.sub_courses?.length || 0 }} Sub-courses</span>
                                                </span>
                                                <span class="flex items-center space-x-1">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                    </svg>
                                                    <span>Main Course</span>
                                                </span>
                                                <span class="flex items-center space-x-1">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                                                    </svg>
                                                    <span>{{ mainCourse.completion_progress }}% Progress</span>
                                                </span>
                                                <span class="flex items-center space-x-1">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11 11-4.925 11-11S18.075 1 12 1zm0 2c4.963 0 9 4.037 9 9s-4.037 9-9 9-9-4.037-9-9 4.037-9 9-9zm-1 4v2.05c-1.71.208-3 .996-3 2.45 0 1.359 1.02 2.142 2.67 2.55l.33.08v3.32c-.92-.13-1.78-.49-2.5-1.03l-.83 1.54c1.03.77 2.33 1.22 3.83 1.34V21h2v-2.05c1.78-.2 3.2-1.09 3.2-2.64 0-1.39-1.02-2.18-2.8-2.63l-.4-.09V10.3c.69.1 1.37.36 1.9.78l.84-1.52c-.8-.6-1.92-1-3.24-1.14V7h-2z"/>
                                                    </svg>
                                                    <span>KES {{ (mainCourse.price || 0).toLocaleString() }}</span>
                                                </span>
                                            </div>
                                            <!-- Certificate Button -->
                                            <div v-if="mainCourse.is_completed">
                                                <button
                                                    @click="downloadCertificate(mainCourse)"
                                                    class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-400 text-yellow-900 px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg font-semibold text-xs sm:text-sm transition-all duration-200 transform hover:scale-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-yellow-400 focus-visible:ring-offset-indigo-600"
                                                >
                                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                        <polyline points="14,2 14,8 20,8"/>
                                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                                        <polyline points="10,9 9,9 8,9"/>
                                                    </svg>
                                                    Get Certificate
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <button v-if="mainCourse.has_access"
                                        @click="toggleCourse(mainCourse.id)"
                                        class="text-white hover:text-yellow-200 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white focus-visible:ring-offset-indigo-600 rounded-md"
                                    >
                                        <svg 
                                            :class="expandedCourses.includes(mainCourse.id) ? 'rotate-180' : ''"
                                            class="w-6 h-6 transition-transform duration-200"
                                            fill="none" 
                                            stroke="currentColor" 
                                            viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Purchase Row (shown when no access) -->
                            <div v-if="!mainCourse.has_access" class="bg-white/10 rounded-lg p-4 border border-white/10 mb-2">
                                <div class="flex flex-col sm:flex-row items-center gap-3">
                                    <div class="text-white font-semibold">Price: KES {{ (mainCourse.price || 0).toLocaleString() }}</div>
                                    <div class="flex items-center gap-2 w-full sm:w-auto">
                                        <button @click="openBuyModal(mainCourse)"
                                            class="inline-flex items-center gap-2 bg-yellow-400 hover:bg-yellow-300 text-yellow-900 px-4 py-2 rounded-md font-semibold transition">
                                            Buy Course
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Sub-courses Section -->
                            <div v-if="mainCourse.has_access && expandedCourses.includes(mainCourse.id)" class="border-t border-gray-100">
                                <div class="p-4 sm:p-6">
                                    <div v-if="mainCourse.sub_courses && mainCourse.sub_courses.length > 0" class="space-y-4">
                                        <div 
                                            v-for="subCourse in mainCourse.sub_courses" 
                                            :key="subCourse.id"
                                            class="bg-gray-50 rounded-xl p-4 sm:p-6 border border-gray-200 hover:border-indigo-300 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5 group"
                                        >
                                            <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-4 items-start">
                                                <!-- Title & description -->
                                                <div class="sm:col-start-1 order-1 sm:order-1">
                                                    <div class="flex items-center space-x-3 mb-2 sm:mb-3">
                                                        <div class="w-7 h-7 sm:w-8 sm:h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                <path d="M9 12l2 2 4-4" />
                                                                <path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9s4.03-9 9-9c1.51 0 2.93.37 4.18 1.03" />
                                                            </svg>
                                                        </div>
                                                        <h3 class="text-base sm:text-lg font-semibold text-gray-800">{{ subCourse.title }}</h3>
                                                    </div>
                                                    <p v-if="subCourse.description" class="text-gray-600 mb-3 sm:mb-4 leading-relaxed text-sm">
                                                        {{ subCourse.description }}
                                                    </p>
                                                </div>

                                                <!-- Actions -->
                                                <div class="order-2 sm:order-1 sm:col-start-2 ml-0 sm:ml-6 flex flex-col gap-2 sm:gap-3 w-full sm:w-auto">
                                                    <div class="flex gap-2 sm:gap-3 flex-col sm:flex-row w_full">
                                                        <button
                                                            @click="handleLearningClick(subCourse)"
                                                            class="inline-flex items-center justify-center gap-2 px-4 py-2 sm:px-6 sm:py-3 text-sm font-semibold rounded-lg text-white shadow-lg transition-all duration-200 tracking-wide bg-gradient-to-r from-indigo-500 to-purple-600 hover:shadow-xl hover:-translate-y-0.5 w-full sm:w-auto focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-white"
                                                        >
                                                            <svg
                                                                class="w-4 h-4 stroke-2"
                                                                viewBox="0 0 24 24"
                                                                fill="none"
                                                                stroke="currentColor"
                                                            >
                                                                <circle cx="12" cy="12" r="10" />
                                                                <polygon points="10,8 16,12 10,16 10,8" />
                                                            </svg>
                                                            Start Learning
                                                        </button>
                                                        <button
                                                            @click="handleQuizClick(subCourse)"
                                                            class="inline-flex items-center justify-center gap-2 px-4 py-2 sm:px-6 sm:py-3 text-sm font-semibold rounded-lg text-white shadow-lg transition-all duration-200 tracking-wide w-full sm:w-auto focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-white"
                                                            :class="{
                                                                'bg-gradient-to-r from-green-500 to-emerald-600 hover:shadow-xl hover:-translate-y-0.5': !subCourse.quiz_status?.completed,
                                                                'bg-gradient-to-r from-blue-500 to-indigo-600 hover:shadow-xl hover:-translate-y-0.5': subCourse.quiz_status?.completed && subCourse.quiz_status?.passed,
                                                                'bg-gradient-to-r from-yellow-500 to-orange-600 hover:shadow-xl hover:-translate-y-0.5': subCourse.quiz_status?.completed && !subCourse.quiz_status?.passed
                                                            }"
                                                        >
                                                            <svg
                                                                class="w-4 h-4 stroke-2"
                                                                viewBox="0 0 24 24"
                                                                fill="none"
                                                                stroke="currentColor"
                                                            >
                                                                <path d="M9 12l2 2 4-4" />
                                                                <path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9s4.03-9 9-9c1.51 0 2.93.37 4.18 1.03" />
                                                            </svg>
                                                            {{ getQuizButtonText(subCourse) }}
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- Quiz Description -->
                                                    <div v-if="subCourse.quiz_status?.completed" class="text-[11px] sm:text-xs text-gray-500 italic max-w-sm">
                                                        Click the quiz button above to view detailed results and reattempt the test
                                                    </div>
                                                    <div v-else class="text-[11px] sm:text-xs text-gray-500 italic max-w-sm">
                                                        Take the quiz to test your knowledge (70% required to pass)
                                                    </div>
                                                </div>

                                                <!-- Features: below on mobile, left on desktop -->
                                                <div class="order-3 sm:order-2 sm:col-start-1 sm:col-span-1 mt-3 sm:mt-0 pt-3 sm:pt-0 border-t sm:border-0">
                                                    <div class="flex flex-wrap gap-3 sm:gap-4">
                                                        <div class="flex items-center gap-2 text-gray-600 text-xs sm:text-sm">
                                                            <svg class="w-4 h-4 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                <path d="M9 11l3 3l8-8" />
                                                                <path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9s4.03-9 9-9c1.51 0 2.93.37 4.18 1.03" />
                                                            </svg>
                                                            <span>Certificate included</span>
                                                        </div>
                                                        <div class="flex items-center gap-2 text-gray-600 text-xs sm:text-sm">
                                                            <svg class="w-4 h-4 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                <path d="M8 6l4-4 4 4" />
                                                                <path d="M12 2v10.3a4 4 0 0 1-1.172 2.872L4 22" />
                                                                <path d="M20 22l-6.828-6.828A4 4 0 0 1 12 12.3" />
                                                            </svg>
                                                            <span>Self-paced learning</span>
                                                        </div>
                                                        <div v-if="subCourse.materials && subCourse.materials.length > 0" class="flex items-center gap-2 text-gray-600 text-xs sm:text-sm">
                                                            <svg class="w-4 h-4 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                                                <polyline points="14,2 14,8 20,8" />
                                                                <line x1="16" y1="13" x2="8" y2="13" />
                                                                <line x1="16" y1="17" x2="8" y2="17" />
                                                                <polyline points="10,9 9,9 8,9" />
                                                            </svg>
                                                            <span>{{ subCourse.materials.length }} material(s)</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div v-else class="text-center py-8">
                                        <div class="text-gray-400">
                                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No sub-courses available</h3>
                                            <p class="text-sm text-gray-500">Sub-courses will be added to this main course soon.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</ElearningSidebar>

    <!-- Reusable Buy Confirmation Modal Component -->
    <BuyConfirmationModal
      v-model:show="showBuyModal"
      v-model:phone="phoneInput"
      :course-title="selectedCourse?.title || ''"
      :price="(selectedCourse?.price) || 0"
      :error="phoneError"
      :is-submitting="isSubmitting"
      @confirm="confirmBuy"
    />
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import ElearningSidebar from "@/Components/ElearningSidebar.vue";
import BuyConfirmationModal from '@/Components/Shared/BuyConfirmationModal.vue';
import { ref, onMounted } from 'vue';

const props = defineProps({
  courses: { type: Array, default: () => [] },
  userPhone: { type: String, default: '' },
});

const expandedCourses = ref([]);
const STORAGE_EXPANDED_KEY = 'elearning.courses.expanded.local';

const saveExpanded = () => {
  try {
    localStorage.setItem(STORAGE_EXPANDED_KEY, JSON.stringify(expandedCourses.value || []));
  } catch (e) {}
};

const restoreExpanded = () => {
  try {
    const raw = localStorage.getItem(STORAGE_EXPANDED_KEY);
    if (!raw) return;
    const parsed = JSON.parse(raw);
    if (Array.isArray(parsed)) expandedCourses.value = parsed;
  } catch (e) {}
};

function toggleCourse(courseId) {
  const index = expandedCourses.value.indexOf(courseId);
  if (index > -1) {
    expandedCourses.value.splice(index, 1);
  } else {
    expandedCourses.value.push(courseId);
  }
  saveExpanded();
}

function getQuizButtonText(subCourse) {
  if (!subCourse.quiz_status?.completed) {
    return 'Attempt Quiz';
  } else if (subCourse.quiz_status?.passed) {
    return `Completed (${subCourse.quiz_status.percentage}%)`;
  } else {
    return `Failed (${subCourse.quiz_status.percentage}%)`;
  }
}

function handleLearningClick(subCourse) {
  saveExpanded();
  router.visit(route('elearning.course', { course: subCourse.id }));
}

function handleQuizClick(subCourse) {
  saveExpanded();
  router.visit(route('elearning.quiz', { course: subCourse.id }));
}

function downloadCertificate(mainCourse) {
  const url = route('elearning.certificate', { course: mainCourse.id });
  window.open(url, '_blank');
}

const showBuyModal = ref(false);
const selectedCourse = ref(null);
const phoneInput = ref("");
const phoneError = ref("");
const isSubmitting = ref(false);

function openBuyModal(course) {
  selectedCourse.value = course;
  phoneInput.value = props.userPhone || "";
  phoneError.value = "";
  showBuyModal.value = true;
}

function confirmBuy() {
  if (!selectedCourse.value) return;
  isSubmitting.value = true;
  router.post(route('elearning.buy', { course: selectedCourse.value.id }), { phone: phoneInput.value }, {
    onError: (errors) => {
      phoneError.value = errors?.phone || '';
      if (!phoneError.value) {
        const msg = errors?.course || 'Unable to start payment. Ensure your profile phone is set.';
        alert(msg);
      }
      isSubmitting.value = false;
    },
  });
}

onMounted(() => {
  restoreExpanded();
});
</script>
