<template>
    <Head title="Course" />
    <Sidebar title="Course Details">
    <div class="bg-gray-50 min-h-screen">
        <!-- Navigation -->
            <div class="bg-white pt-6 pb-6 border-b border-gray-200">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <button
                    @click="$inertia.visit(route('elearning.courses'))"
                    class="inline-flex items-center gap-2 bg-transparent border-none cursor-pointer text-base font-semibold px-4 py-2 rounded-lg transition-colors duration-200 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500"
                    style="color: #667eea;"
                >
                    <svg
                        class="w-5 h-5 stroke-2"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                    >
                        <path d="M19 12H5" />
                        <path d="M12 19l-7-7 7-7" />
                    </svg>
                    Back to Courses
                </button>
            </div>
        </div>

        <!-- Course Hero Section -->
        <div 
            class="text-white py-12 md:py-16"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"
        >
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-[1fr_350px] gap-12 md:gap-16 items-start">
                    <div class="hero-main">
                        <h1 class="text-4xl lg:text-5xl font-bold mb-6 leading-tight">{{ course.title }}</h1>
                        <p class="text-lg leading-relaxed opacity-95">
                            {{ course.description }}
                        </p>
                    </div>

                    <div class="hero-sidebar">
                        <div class="bg-white rounded-2xl overflow-hidden shadow-xl sticky top-8">
                            <div class="p-8">
                                <div class="course-includes">
                                    <h4 class="text-lg font-semibold mb-4 text-gray-800">This course includes:</h4>
                                    <ul class="list-none p-0 m-0">
                                        <li
                                            v-for="material in course.materials"
                                            :key="material.id"
                                            class="flex items-center gap-3 mb-3 text-gray-600 text-sm"
                                        >
                                            <svg
                                                class="w-4 h-4 stroke-2 flex-shrink-0"
                                                style="color: #667eea;"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                            >
                                                <path d="M9 11l3 3l8-8" />
                                            </svg>
                                            {{ material.title }} (PDF)
                                        </li>
                                        <li class="flex items-center gap-3 mb-3 text-gray-600 text-sm">
                                            <svg
                                                class="w-4 h-4 stroke-2 flex-shrink-0"
                                                style="color: #667eea;"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                            >
                                                <path d="M9 11l3 3l8-8" />
                                            </svg>
                                            Certificate of completion
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Content Section -->
        <div class="py-16 bg-gray-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-12">
                    <div class="main-content">
                        <div class="materials-section mb-8">
                            <h2 class="text-3xl font-semibold text-gray-800 mb-8">Course Materials</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                                <div
                                    v-for="material in course.materials"
                                    :key="material.id"
                                    class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md focus-within:ring-2 focus-within:ring-indigo-500"
                                >
                                    <div class="flex items-center gap-3 mb-5">
                                        <svg
                                            class="w-6 h-6 flex-shrink-0"
                                            style="color: #667eea;"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                        >
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                                <polyline points="14,2 14,8 20,8" />
                                                <line x1="16" y1="13" x2="8" y2="13" />
                                                <line x1="16" y1="17" x2="8" y2="17" />
                                                <polyline points="10,9 9,9 8,9" />
                                        </svg>
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-gray-800">{{ material.title }}</h3>
                                                <p class="text-sm text-gray-500">{{ material.file_size }}</p>
                                            </div>
                                    </div>
                                    <div class="flex justify-end">
                                        <button
                                                @click="openPdfInNewTab(material, $event)"
                                            class="w-full md:w-auto text-white border-none py-2.5 px-5 rounded-lg font-medium text-sm cursor-pointer transition-all duration-200 hover:-translate-y-1 hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500"
                                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"
                                        >
                                            View PDF
                                        </button>
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
import Sidebar from '@/Components/Sidebar.vue';

export default {
    components: { Sidebar },
    props: {
    course: Object,
        materials: Array,
    },
    methods: {
        openPdfInNewTab(material, event) {
  // Prevent default behavior
  event.preventDefault();
  
  // Create the viewer URL
  const viewerUrl = route('course-materials.viewer', { 
    material: material.id,
    t: Date.now() // Cache buster
  });

  // Open in new tab (same window)
  window.open(viewerUrl, '_blank');
        },
    },
};
</script>