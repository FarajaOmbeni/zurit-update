<template>
  <AdminSidebar title="Manage Courses">
    <div class="px-4 sm:px-6 lg:px-8">
      <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
          <h1 class="text-xl font-semibold text-gray-900">Courses</h1>
          <p class="mt-2 text-sm text-gray-700">Manage all financial literacy courses</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none space-x-3">
          <button
            type="button"
            @click="$inertia.visit(route('admin.courses.create-main'))"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
          >
            Create Main Course
          </button>
          <button
            type="button"
            @click="$inertia.visit(route('admin.courses.create'))"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
          >
            Add Sub-Course
          </button>
        </div>
      </div>

      <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
              <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Main Course</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Sub-Courses</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Created</th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                      <span class="sr-only">Actions</span>
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <template v-for="mainCourse in mainCourses" :key="mainCourse.id">
                    <!-- Main course row -->
                    <tr class="bg-white">
                      <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                        <div class="flex items-center">
                          <button
                            @click="toggleSubCourses(mainCourse.id)"
                            class="mr-2 text-gray-400 hover:text-gray-600"
                          >
                            <svg 
                              :class="expandedCourses.includes(mainCourse.id) ? 'rotate-90' : ''"
                              class="w-4 h-4 transition-transform duration-200"
                              fill="none" 
                              stroke="currentColor" 
                              viewBox="0 0 24 24"
                            >
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                          </button>
                          {{ mainCourse.title }}
                        </div>
                      </td>
                      <td class="px-3 py-4 text-sm text-gray-500">
                        <span class="text-gray-400">{{ mainCourse.sub_courses?.length || 0 }} sub-course(s)</span>
                      </td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        {{ new Date(mainCourse.created_at).toLocaleDateString() }}
                      </td>
                      <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <button
                          @click="$inertia.visit(route('admin.courses.edit', { course: mainCourse.id }))"
                          class="text-purple-600 hover:text-purple-900 mr-4"
                        >
                          Edit
                        </button>
                        <button
                          @click="confirmDelete(mainCourse.id)"
                          class="text-red-600 hover:text-red-900"
                        >
                          Delete
                        </button>
                      </td>
                    </tr>
                    
                    <!-- Sub-courses row (shown when expanded) -->
                    <tr v-if="expandedCourses.includes(mainCourse.id)" class="bg-gray-50">
                      <td colspan="4" class="px-0 py-0">
                        <div class="border-t border-gray-200">
                          <div class="px-6 py-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Sub-Courses for "{{ mainCourse.title }}"</h4>
                            
                            <div v-if="mainCourse.sub_courses && mainCourse.sub_courses.length > 0">
                              <div class="space-y-3">
                                <div v-for="subCourse in mainCourse.sub_courses" :key="subCourse.id" class="bg-white rounded-lg border border-gray-200 p-4">
                                  <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                      <h5 class="text-sm font-medium text-gray-900">{{ subCourse.title }}</h5>
                                      <p v-if="subCourse.description" class="text-sm text-gray-500 mt-1">
                                        {{ subCourse.description.substring(0, 100) }}{{ subCourse.description.length > 100 ? '...' : '' }}
                                      </p>
                                      <div v-if="subCourse.materials && subCourse.materials.length > 0" class="mt-2">
                                        <span class="text-xs text-gray-400">{{ subCourse.materials.length }} material(s)</span>
                                      </div>
                                    </div>
                                    <div class="flex items-center space-x-2 ml-4">
                                      <button
                                        @click="$inertia.visit(route('admin.quizzes.create'))"
                                        class="text-green-600 hover:text-green-900 text-sm"
                                      >
                                        Add Quiz
                                      </button>
                                      <button
                                        @click="$inertia.visit(route('admin.courses.edit', { course: subCourse.id }))"
                                        class="text-purple-600 hover:text-purple-900 text-sm"
                                      >
                                        Edit
                                      </button>
                                      <button
                                        @click="confirmDelete(subCourse.id)"
                                        class="text-red-600 hover:text-red-900 text-sm"
                                      >
                                        Delete
                                      </button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            
                            <div v-else class="text-center py-6">
                              <div class="text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No sub-courses available</h3>
                                <p class="mt-1 text-sm text-gray-500">No sub-courses have been created for this course yet.</p>
                                <div class="mt-6">
                                  <button
                                    @click="$inertia.visit(route('admin.courses.create'))"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                                  >
                                    Add Sub-Course
                                  </button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminSidebar>
</template>

<script setup>
import AdminSidebar from '@/Components/AdminSidebar.vue';
import { router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
  courses: Object,
});

const expandedCourses = ref([]);

const mainCourses = computed(() => {
  return props.courses.data;
});

const toggleSubCourses = (courseId) => {
  const index = expandedCourses.value.indexOf(courseId);
  if (index > -1) {
    expandedCourses.value.splice(index, 1);
  } else {
    expandedCourses.value.push(courseId);
  }
};

const confirmDelete = (id) => {
  if (confirm('Are you sure you want to delete this course? This will also delete all associated materials.')) {
    router.delete(route('admin.courses.destroy', { course: id }));
  }
};
</script>