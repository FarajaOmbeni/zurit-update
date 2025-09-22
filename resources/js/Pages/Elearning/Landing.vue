<template>

    <Head title="E-Learning" />
    <ElearningSidebar title="E-Learning">
        <DashboardBackButton />
        <div class="min-h-screen">
            <!-- Hero Section -->
            <header class="text-white py-20 relative overflow-hidden pt-32"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="absolute inset-0 bg-black bg-opacity-10 z-10"></div>
                <div class="max-w-6xl mx-auto pt-16 md:pt-20 px-4 sm:px-6 lg:px-8 relative z-20 text-center">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-6 leading-tight">
                        Financial Literacy
                        <span
                            class="block bg-gradient-to-r from-yellow-300 to-pink-300 bg-clip-text text-transparent">Made
                            Simple</span>
                    </h1>
                    <p class="text-xl max-w-2xl mx-auto mb-10 opacity-90 leading-relaxed">
                        Master personal finance with our comprehensive, self-paced courses
                        designed for learners at every level
                    </p>
                    <div class="flex justify-center gap-4 mb-12 flex-wrap">
                        <button @click="$inertia.visit(route('elearning.courses'))"
                            class="px-8 py-3 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 hover:brightness-110 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            Browse All Courses
                        </button>
                    </div>
                </div>
            </header>

            <!-- Featured Courses Section -->
            <section class="py-24 bg-gray-50">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Featured Courses</h2>
                        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                            Start your financial journey with our most popular main courses
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div v-for="course in featuredCourses" :key="course.id"
                            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all hover:-translate-y-1 hover:shadow-md focus-within:ring-2 focus-within:ring-indigo-500 flex flex-col h-full">
                            <div class="p-6 pb-0 flex justify-between items-start">
                                <span
                                    class="px-3 py-1 text-white text-xs font-semibold rounded-full uppercase tracking-wider"
                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    Main Course
                                </span>
                            </div>

                            <div class="p-6 flex-grow">
                                <h3 class="text-xl font-semibold text-gray-800 mb-3">{{ course.title }}</h3>
                                <div class="flex items-center space-x-4 text-gray-600 mb-4">
                                    <span class="flex items-center space-x-1 text-sm">
                                        <svg class="w-4 h-4 text-indigo-500" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor">
                                            <path d="M9 12l2 2 4-4" />
                                            <path
                                                d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9s4.03-9 9-9c1.51 0 2.93.37 4.18 1.03" />
                                        </svg>
                                        <span>{{ course.sub_courses?.length || 0 }} Sub-courses</span>
                                    </span>
                                    <span class="flex items-center space-x-1 text-sm">
                                        <svg class="w-4 h-4 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M12 1C5.9 1 1 5.9 1 12s4.9 11 11 11 11-4.9 11-11S18.1 1 12 1zm-1 6v2.05c-1.71.21-3 .99-3 2.45 0 1.36 1.02 2.14 2.67 2.55l.33.08v3.32c-.92-.13-1.78-.49-2.5-1.03l-.83 1.54c1.03.77 2.33 1.22 3.83 1.34V21h2v-2.05c1.78-.2 3.2-1.09 3.2-2.64 0-1.39-1.02-2.18-2.8-2.63l-.4-.09V10.3c.69.1 1.37.36 1.9.78l.84-1.52c-.8-.6-1.92-1-3.24-1.14V7h-2z" />
                                        </svg>
                                        <span>KES {{ (course.price || 0).toLocaleString() }}</span>
                                    </span>
                                </div>

                                <!-- Sub-courses preview -->
                                <div v-if="course.sub_courses && course.sub_courses.length > 0" class="mb-4">
                                    <p class="text-sm text-gray-500 mb-2">Includes:</p>
                                    <div class="space-y-1">
                                        <div v-for="subCourse in course.sub_courses.slice(0, 2)" :key="subCourse.id"
                                            class="flex items-center space-x-2">
                                            <div class="w-2 h-2 bg-indigo-400 rounded-full"></div>
                                            <span class="text-sm text-gray-600">{{ subCourse.title }}</span>
                                        </div>
                                        <div v-if="course.sub_courses.length > 2" class="text-xs text-gray-400 ml-4">
                                            +{{ course.sub_courses.length - 2 }} more sub-courses
                                        </div>
                                    </div>
                                </div>

                                <div v-else class="text-sm text-gray-500 mb-4">
                                    Sub-courses coming soon
                                </div>
                            </div>

                            <div class="p-6 pt-0 mt-auto space-y-2">
                                <button v-if="course.has_access"
                                    @click="$inertia.visit(route('elearning.courses'))"
                                    class="w-full px-4 py-2 border-2 font-medium rounded-lg transition-colors hover:text-white hover:shadow focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500 hover:bg-gradient-to-br hover:from-indigo-500 hover:to-purple-600"
                                    style="border-color: #667eea; color: #667eea;">
                                    Explore Courses
                                </button>
                                <button v-else disabled
                                    class="w-full px-4 py-2 border-2 font-medium rounded-lg opacity-60 cursor-not-allowed"
                                    style="border-color: #667eea; color: #667eea;">
                                    Explore Courses (Locked)
                                </button>
                                <button v-if="!course.has_access"
                                    @click="openBuyModal(course)"
                                    class="w-full px-4 py-2 bg-yellow-400 text-yellow-900 font-semibold rounded-lg hover:bg-yellow-300 transition">
                                    Buy Course
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section class="py-24 bg-white">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                        <div class="text-center">
                            <div class="w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-6 text-white shadow-lg"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Expert-Led Content</h3>
                            <p class="text-gray-600">Learn from certified financial advisors and industry experts</p>
                        </div>

                        <div class="text-center">
                            <div class="w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-6 text-white shadow-lg"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M14 9V5a3 3 0 0 0-6 0v4" />
                                    <rect x="2" y="9" width="20" height="12" rx="2" ry="2" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Lifetime Access</h3>
                            <p class="text-gray-600">Access your courses anytime, anywhere with our mobile-friendly
                                platform</p>
                        </div>

                        <div class="text-center">
                            <div class="w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-6 text-white shadow-lg"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Interactive Learning</h3>
                            <p class="text-gray-600">Engage with quizzes, simulations, and real-world case studies</p>
                        </div>
                    </div>
                </div>
            </section>
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
import { ref } from 'vue';
import ElearningSidebar from '@/Components/ElearningSidebar.vue';
import DashboardBackButton from '@/Components/Shared/DashboardBackButton.vue';
import BuyConfirmationModal from '@/Components/Shared/BuyConfirmationModal.vue';

const props = defineProps({
  featuredCourses: { type: Array, default: () => [] },
  userPhone: { type: String, default: '' },
});

const showBuyModal = ref(false);
const selectedCourse = ref(null);
const phoneInput = ref('');
const phoneError = ref('');
const isSubmitting = ref(false);

function openBuyModal(course) {
  selectedCourse.value = course;
  phoneInput.value = props.userPhone || '';
  phoneError.value = '';
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
</script>
