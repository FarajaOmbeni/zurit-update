<template>
    <Head title="Courses" />
    <Sidebar title="All Courses">
        <div class="bg-gray-50 min-h-screen">
            <!-- Page Header -->
            <div
                class="relative text-white pt-56 pb-16 overflow-hidden"
                style="
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                "
            >
                <div class="absolute inset-0 bg-black bg-opacity-10 z-10"></div>

                <div class="max-w-6xl mx-auto px-8 relative z-20">
                    <div class="text-center mb-12">
                        <h1
                            class="text-5xl md:text-6xl font-extrabold mb-4 tracking-tight"
                        >
                            All Courses
                        </h1>
                        <p
                            class="text-xl opacity-90 max-w-2xl mx-auto leading-relaxed"
                        >
                            Explore our comprehensive collection of financial
                            literacy courses
                        </p>
                    </div>

                    <!-- Course Stats -->
                    <div class="flex justify-center gap-12 flex-wrap">
                        <div class="text-center">
                            <div
                                class="text-4xl md:text-5xl font-bold mb-2 text-yellow-200"
                            >
                                {{ courses.length }}
                            </div>
                            <div
                                class="text-sm opacity-80 uppercase tracking-wider"
                            >
                                Main Courses
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Filters -->
            <div class="bg-white py-8 border-b border-gray-200 sticky top-0 z-50">
                <div class="max-w-6xl mx-auto px-8">
                    <div class="flex gap-4 justify-center flex-wrap">
                        <button
                            class="px-6 py-3 border-2 border-indigo-500 bg-indigo-500 text-white rounded-full font-semibold cursor-pointer transition-all duration-200 hover:bg-indigo-600 hover:border-indigo-600"
                        >
                            All Courses
                        </button>
                    </div>
                </div>
            </div>

            <!-- Course Content -->
            <div class="py-16 bg-gray-50 min-h-[60vh]">
                <div class="max-w-6xl mx-auto px-8">
                    <!-- Empty State -->
                    <div
                        v-if="courses.length === 0"
                        class="text-center py-16 px-8 max-w-lg mx-auto"
                    >
                        <div
                            class="w-20 h-20 mx-auto mb-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center text-white"
                        >
                            <svg
                                class="w-10 h-10 stroke-2"
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
                        <h3 class="text-2xl font-semibold text-gray-800 mb-4">
                            No courses available yet
                        </h3>
                        <p class="text-gray-600 leading-relaxed mb-8">
                            We're working hard to bring you amazing financial
                            literacy courses. Check back soon for updates!
                        </p>
                        <button
                            class="inline-flex items-center gap-2 px-6 py-3.5 text-sm font-semibold rounded-lg text-white bg-gradient-to-r from-indigo-500 to-purple-600 shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200"
                        >
                            Get Notified When Available
                        </button>
                    </div>

                    <!-- Course Grid -->
                    <div
                        v-else
                        class="space-y-8"
                    >
                        <div
                            v-for="mainCourse in courses"
                            :key="mainCourse.id"
                            class="bg-white rounded-2xl overflow-hidden transition-all duration-300 shadow-sm border border-gray-200 hover:shadow-xl"
                        >
                            <!-- Main Course Header -->
                            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-8">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                                            <svg
                                                class="w-6 h-6 text-white stroke-2"
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
                                            <div class="flex items-center gap-4 mb-2">
                                                <h2 class="text-2xl font-bold text-white">
                                                    {{ mainCourse.title }}
                                                </h2>
                                                <div v-if="mainCourse.is_completed" class="flex items-center gap-2 bg-green-500 bg-opacity-20 px-3 py-1 rounded-full">
                                                    <svg class="w-4 h-4 text-green-200" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path d="M9 12l2 2 4-4" />
                                                        <path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9s4.03-9 9-9c1.51 0 2.93.37 4.18 1.03" />
                                                    </svg>
                                                    <span class="text-green-200 text-sm font-medium">Completed</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-4 text-white text-sm opacity-90 mb-3">
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
                                            </div>
                                            <!-- Certificate Button -->
                                            <div v-if="mainCourse.is_completed">
                                                <button
                                                    @click="downloadCertificate(mainCourse)"
                                                    class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-400 text-yellow-900 px-4 py-2 rounded-lg font-semibold text-sm transition-all duration-200 transform hover:scale-105"
                                                >
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
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
                                    <button
                                        @click="toggleCourse(mainCourse.id)"
                                        class="text-white hover:text-yellow-200 transition-colors"
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

                            <!-- Sub-courses Section -->
                            <div v-if="expandedCourses.includes(mainCourse.id)" class="border-t border-gray-100">
                                <div class="p-6">
                                    <div v-if="mainCourse.sub_courses && mainCourse.sub_courses.length > 0" class="space-y-4">
                                        <div 
                                            v-for="subCourse in mainCourse.sub_courses" 
                                            :key="subCourse.id"
                                            class="bg-gray-50 rounded-xl p-6 border border-gray-200 hover:border-indigo-300 transition-all duration-200 hover:shadow-md"
                                        >
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-3 mb-3">
                                                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                <path d="M9 12l2 2 4-4" />
                                                                <path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9s4.03-9 9-9c1.51 0 2.93.37 4.18 1.03" />
                                                            </svg>
                                                        </div>
                                                        <h3 class="text-lg font-semibold text-gray-800">{{ subCourse.title }}</h3>
                                                    </div>
                                                    
                                                    <p v-if="subCourse.description" class="text-gray-600 mb-4 leading-relaxed">
                                                        {{ subCourse.description }}
                                                    </p>

                                                    <!-- Course Features -->
                                                    <div class="flex flex-wrap gap-4 mb-4">
                                                        <div class="flex items-center gap-2 text-gray-600 text-sm">
                                                            <svg class="w-4 h-4 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                <path d="M9 11l3 3l8-8" />
                                                                <path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9s4.03-9 9-9c1.51 0 2.93.37 4.18 1.03" />
                                                            </svg>
                                                            <span>Certificate included</span>
                                                        </div>
                                                        <div class="flex items-center gap-2 text-gray-600 text-sm">
                                                            <svg class="w-4 h-4 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                <path d="M8 6l4-4 4 4" />
                                                                <path d="M12 2v10.3a4 4 0 0 1-1.172 2.872L4 22" />
                                                                <path d="M20 22l-6.828-6.828A4 4 0 0 1 12 12.3" />
                                                            </svg>
                                                            <span>Self-paced learning</span>
                                                        </div>
                                                        <div v-if="subCourse.materials && subCourse.materials.length > 0" class="flex items-center gap-2 text-gray-600 text-sm">
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
                                                
                                                                                                <div class="ml-6 flex flex-col gap-3">
                                                    <div class="flex gap-3">
                                                        <button
                                                            @click="handleLearningClick(subCourse)"
                                                            class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold rounded-lg text-white shadow-lg transition-all duration-200 tracking-wide bg-gradient-to-r from-indigo-500 to-purple-600 hover:shadow-xl hover:-translate-y-0.5"
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
                                                            class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold rounded-lg text-white shadow-lg transition-all duration-200 tracking-wide"
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
                                                    <div v-if="subCourse.quiz_status?.completed" class="text-xs text-gray-500 italic max-w-sm">
                                                        Click the quiz button above to view detailed results and reattempt the test
                                                    </div>
                                                    <div v-else class="text-xs text-gray-500 italic max-w-sm">
                                                        Take the quiz to test your knowledge (70% required to pass)
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
    </Sidebar>
</template>

<script>
import Sidebar from "@/Components/Sidebar.vue";
import { ref } from 'vue';

export default {
    components: { Sidebar },
    props: {
        courses: Array,
    },
    setup() {
        const expandedCourses = ref([]);

        const toggleCourse = (courseId) => {
            const index = expandedCourses.value.indexOf(courseId);
            if (index > -1) {
                expandedCourses.value.splice(index, 1);
            } else {
                expandedCourses.value.push(courseId);
            }
        };

        const getQuizButtonText = (subCourse) => {
            if (!subCourse.quiz_status?.completed) {
                return 'Attempt Quiz';
            } else if (subCourse.quiz_status?.passed) {
                return `Completed (${subCourse.quiz_status.percentage}%)`;
            } else {
                return `Failed (${subCourse.quiz_status.percentage}%)`;
            }
        };

        const handleLearningClick = (subCourse) => {
            window.location.href = route('elearning.course', { course: subCourse.id });
        };

        const handleQuizClick = (subCourse) => {
            window.location.href = route('elearning.quiz', { course: subCourse.id });
        };

        const downloadCertificate = (mainCourse) => {
            window.location.href = route('elearning.certificate', { course: mainCourse.id });
        };

        return {
            expandedCourses,
            toggleCourse,
            getQuizButtonText,
            handleLearningClick,
            handleQuizClick,
            downloadCertificate
        };
    }
};
</script>
