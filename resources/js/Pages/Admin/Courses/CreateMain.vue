<template>
  <AdminSidebar title="Create Main Course">
    <div class="max-w-3xl mx-auto py-10 sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900 mb-6">Create Main Course</h2>
          
          <form @submit.prevent="submit">
            <div class="mb-6">
              <label for="title" class="block text-sm font-medium text-gray-700">Course Title</label>
              <input
                v-model="form.title"
                type="text"
                id="title"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                placeholder="Enter the main course title"
                required
              >
              <p v-if="form.errors.title" class="mt-2 text-sm text-red-600">
                {{ form.errors.title }}
              </p>
            </div>

            <div class="flex items-center justify-end pt-6 border-t border-gray-200">
              <button
                type="button"
                @click="$inertia.visit(route('admin.courses.index'))"
                class="mr-4 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                :disabled="form.processing"
              >
                <span v-if="form.processing">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Saving...
                </span>
                <span v-else>Create Main Course</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminSidebar>
</template>

<script setup>
import AdminSidebar from '@/Components/AdminSidebar.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  title: ''
});

const submit = () => {
  form.post(route('admin.courses.store'), {
    onSuccess: () => {
      form.reset();
    }
  });
};
</script>

<style scoped>
.material-item {
  background-color: #f9fafb;
  border-color: #e5e7eb;
}

.material-item:last-of-type {
  margin-bottom: 0;
}
</style> 