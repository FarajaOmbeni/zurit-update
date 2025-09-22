<template>
    <Head title="Course" />
    <Sidebar title="Course Details">
        <div class="bg-gray-50 min-h-screen">
            <!-- Navigation -->
            <div class="bg-white pt-6 pb-6 border-b border-gray-200">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <button
                        @click="goBack()"
                        class="inline-flex items-center gap-2 bg-transparent border-none cursor-pointer text-base font-semibold px-4 py-2 rounded-lg transition-colors duration-200 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500"
                        style="color: #667eea"
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
                style="
                    background: linear-gradient(
                        135deg,
                        #667eea 0%,
                        #764ba2 100%
                    );
                "
            >
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div
                        class="grid grid-cols-1 lg:grid-cols-[1fr_350px] gap-12 md:gap-16 items-start"
                    >
                        <div class="hero-main">
                            <h1
                                class="text-4xl lg:text-5xl font-bold mb-6 leading-tight"
                            >
                                {{ course.title }}
                            </h1>
                            <p class="text-lg leading-relaxed opacity-95">
                                {{ course.description }}
                            </p>
                        </div>

                        <div class="hero-sidebar">
                            <div
                                class="bg-white rounded-2xl overflow-hidden shadow-xl sticky top-8"
                            >
                                <div class="p-8">
                                    <div class="course-includes">
                                        <h4
                                            class="text-lg font-semibold mb-4 text-gray-800"
                                        >
                                            This course includes:
                                        </h4>
                                        <ul class="list-none p-0 m-0">
                                            <li
                                                v-for="material in course.materials"
                                                :key="material.id"
                                                class="flex items-center gap-3 mb-3 text-gray-600 text-sm"
                                            >
                                                <svg
                                                    class="w-4 h-4 stroke-2 flex-shrink-0"
                                                    style="color: #667eea"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                >
                                                    <path d="M9 11l3 3l8-8" />
                                                </svg>
                                                {{ material.title }} ({{
                                                    material.is_video
                                                        ? "Video"
                                                        : "PDF"
                                                }})
                                            </li>
                                            <li
                                                class="flex items-center gap-3 mb-3 text-gray-600 text-sm"
                                            >
                                                <svg
                                                    class="w-4 h-4 stroke-2 flex-shrink-0"
                                                    style="color: #667eea"
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
                                <h2
                                    class="text-3xl font-semibold text-gray-800 mb-8"
                                >
                                    Course Materials
                                </h2>

                                <!-- Group materials by base title -->
                                <div
                                    v-for="(
                                        group, groupTitle
                                    ) in groupedMaterials"
                                    :key="groupTitle"
                                    class="mb-8"
                                >
                                    <h3
                                        class="text-xl font-medium text-gray-800 mb-4"
                                    >
                                        {{ groupTitle }}
                                    </h3>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-6"
                                    >
                                        <div
                                            v-for="material in group"
                                            :key="material.id"
                                            class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md focus-within:ring-2 focus-within:ring-indigo-500"
                                        >
                                            <!-- Document Thumbnail Preview -->
                                            <div
                                                v-if="!material.is_video"
                                                class="mb-4"
                                            >
                                                <div
                                                    class="relative bg-gradient-to-br from-blue-100 to-cyan-100 rounded-lg h-40 flex items-center justify-center border border-blue-200"
                                                >
                                                    <div class="text-center">
                                                        <div
                                                            class="w-16 h-16 mx-auto mb-2 bg-blue-600 rounded-lg flex items-center justify-center shadow-lg"
                                                        >
                                                            <svg
                                                                class="w-8 h-8 text-white"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <path
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                                />
                                                            </svg>
                                                        </div>
                                                        <p
                                                            class="text-blue-700 font-medium"
                                                        >
                                                            PDF Document
                                                        </p>
                                                        <p
                                                            class="text-blue-500 text-sm"
                                                        >
                                                            {{
                                                                material.file_size
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Video Thumbnail Preview -->
                                            <div
                                                v-if="material.is_video"
                                                class="mb-4"
                                            >
                                                <div
                                                    class="relative bg-gradient-to-br from-purple-100 to-indigo-100 rounded-lg h-40 flex items-center justify-center border border-purple-200"
                                                >
                                                    <div class="text-center">
                                                        <div
                                                            class="w-16 h-16 mx-auto mb-2 bg-purple-600 rounded-full flex items-center justify-center shadow-lg"
                                                        >
                                                            <svg
                                                                class="w-8 h-8 text-white"
                                                                fill="currentColor"
                                                                viewBox="0 0 20 20"
                                                            >
                                                                <path
                                                                    d="M6.3 2.84A1 1 0 004 3.71v12.58a1 1 0 001.6.8l8.8-6.29a1 1 0 000-1.6L5.6 3.04a1 1 0 00-.7-.2 1 1 0 00-.6.2z"
                                                                />
                                                            </svg>
                                                        </div>
                                                        <p
                                                            class="text-purple-700 font-medium"
                                                        >
                                                            Video Content
                                                        </p>
                                                        <p
                                                            class="text-purple-500 text-sm"
                                                        >
                                                            {{
                                                                material.file_size
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                class="flex items-center gap-3 mb-5"
                                            >
                                                <!-- PDF Icon -->
                                                <svg
                                                    v-if="!material.is_video"
                                                    class="w-6 h-6 flex-shrink-0"
                                                    style="color: #667eea"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                                                    />
                                                    <polyline
                                                        points="14,2 14,8 20,8"
                                                    />
                                                    <line
                                                        x1="16"
                                                        y1="13"
                                                        x2="8"
                                                        y2="13"
                                                    />
                                                    <line
                                                        x1="16"
                                                        y1="17"
                                                        x2="8"
                                                        y2="17"
                                                    />
                                                    <polyline
                                                        points="10,9 9,9 8,9"
                                                    />
                                                </svg>

                                                <!-- Video Icon -->
                                                <svg
                                                    v-else
                                                    class="w-6 h-6 flex-shrink-0"
                                                    style="color: #667eea"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                >
                                                    <polygon
                                                        points="23,7 16,12 23,17"
                                                    />
                                                    <rect
                                                        x="1"
                                                        y="5"
                                                        width="15"
                                                        height="14"
                                                        rx="2"
                                                        ry="2"
                                                    />
                                                </svg>

                                                <div class="flex-1">
                                                    <h3
                                                        class="font-semibold text-gray-800"
                                                    >
                                                        {{ material.title }}
                                                    </h3>
                                                    <p
                                                        class="text-sm text-gray-500"
                                                    >
                                                        {{ material.file_size }}
                                                        •
                                                        {{
                                                            material.is_video
                                                                ? "Video"
                                                                : "PDF"
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                            <!-- Action Button -->
                                            <div class="flex justify-end">
                                                <button
                                                    @click="
                                                        material.is_video
                                                            ? openVideoModal(
                                                                  material,
                                                              )
                                                            : openPdfInNewTab(
                                                                  material,
                                                                  $event,
                                                              )
                                                    "
                                                    :disabled="
                                                        isLoading(material.id)
                                                    "
                                                    class="w-full md:w-auto text-white border-none py-2.5 px-5 rounded-lg font-medium text-sm cursor-pointer transition-all duration-200 hover:-translate-y-1 hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500 flex items-center justify-center gap-2"
                                                    :class="{
                                                        'opacity-70 cursor-not-allowed':
                                                            isLoading(
                                                                material.id,
                                                            ),
                                                    }"
                                                    style="
                                                        background: linear-gradient(
                                                            135deg,
                                                            #667eea 0%,
                                                            #764ba2 100%
                                                        );
                                                    "
                                                >
                                                    <!-- Loading spinner -->
                                                    <svg
                                                        v-if="
                                                            isLoading(
                                                                material.id,
                                                            )
                                                        "
                                                        class="animate-spin h-4 w-4 text-white"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <circle
                                                            class="opacity-25"
                                                            cx="12"
                                                            cy="12"
                                                            r="10"
                                                            stroke="currentColor"
                                                            stroke-width="4"
                                                        ></circle>
                                                        <path
                                                            class="opacity-75"
                                                            fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                        ></path>
                                                    </svg>

                                                    <!-- Button text -->
                                                    <span
                                                        v-if="material.is_video"
                                                    >
                                                        {{
                                                            isLoading(
                                                                material.id,
                                                            )
                                                                ? "Loading..."
                                                                : "Watch Video"
                                                        }}
                                                    </span>
                                                    <span v-else>
                                                        {{
                                                            isLoading(
                                                                material.id,
                                                            )
                                                                ? "Opening..."
                                                                : "View PDF"
                                                        }}
                                                    </span>
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
        </div>
    </Sidebar>
</template>

<script>
import Sidebar from "@/Components/Sidebar.vue";

export default {
    components: { Sidebar },
    props: {
        course: Object,
        materials: Array,
    },
    data() {
        return {
            loadingMaterials: new Set(), // Track which materials are loading
        };
    },
    computed: {
        groupedMaterials() {
            const groups = {};

            this.course.materials.forEach((material) => {
                // Remove (PDF) or (Video) suffix to get base title
                let baseTitle = material.title.replace(
                    /\s*\((PDF|Video)\)$/,
                    "",
                );

                if (!groups[baseTitle]) {
                    groups[baseTitle] = [];
                }
                groups[baseTitle].push(material);
            });

            return groups;
        },
    },
    methods: {
        goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                this.$inertia.visit(route("elearning.courses"), {
                    preserveScroll: true,
                });
            }
        },
        openPdfInNewTab(material, event) {
            // Prevent default behavior
            event.preventDefault();

            // Set loading state
            this.loadingMaterials.add(material.id);

            // Create the viewer URL
            const viewerUrl = route("course-materials.viewer", {
                material: material.id,
                t: Date.now(), // Cache buster
            });

            // Open in new tab
            const newWindow = window.open(viewerUrl, "_blank");

            // Remove loading state after a short delay (window opening)
            setTimeout(() => {
                this.loadingMaterials.delete(material.id);
            }, 1000);

            // Also remove loading state if window couldn't open
            if (!newWindow) {
                this.loadingMaterials.delete(material.id);
            }
        },
        isLoading(materialId) {
            return this.loadingMaterials.has(materialId);
        },
        openVideoModal(material) {
            // For now, just redirect to the video URL
            // You could implement a modal here if needed
            window.open(material.display_url, "_blank");
        },
    },
};
</script>
