<template>
    <div class="p-6">
        <!-- Search Input -->
        <div class="mb-4">
            <input type="text" v-model="searchQuery" placeholder="Search..."
                class="w-full px-4 py-2 border border-purple-900 rounded focus:outline-none focus:ring-2 focus:ring-yellow-500" />
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-purple-900 text-white">
                    <tr>
                        <th v-for="(header, index) in headers" :key="index" class="px-6 py-3 text-left">
                            {{ header.label }}
                        </th>
                        <th v-if="editable" class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in paginatedData" :key="index"
                        class="border-b border-gray-200 hover:text-black hover:bg-slate-100 hover:text-black">
                        <!-- Loop over headers to display each data field -->
                        <td v-for="(header, index) in headers" :key="index"
                            class="px-6 py-4">
                            {{ item[header.key] }}
                        </td>
                        <!-- Conditionally show actions -->
                        <td v-if="editable" class="px-6 py-4 w-[12rem]">
                            <button @click="$emit('edit', item)"
                                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 mr-2">
                                Edit
                            </button>
                            <button @click="$emit('delete', item)"
                                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                Delete
                            </button>
                        </td>
                    </tr>
                    <tr v-if="paginatedData.length === 0">
                        <td :colspan="editable ? headers.length + 1 : headers.length"
                            class="px-6 py-4 text-center text-gray-500">
                            No records found
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div class="mt-4 flex justify-between items-center">
            <button @click="prevPage" :disabled="currentPage === 1"
                class="px-4 py-2 bg-purple-900 text-white rounded disabled:opacity-50">
                Previous
            </button>
            <div class="text-purple-900">
                Page {{ currentPage }} of {{ totalPages }}
            </div>
            <button @click="nextPage" :disabled="currentPage === totalPages"
                class="px-4 py-2 bg-purple-900 text-white rounded disabled:opacity-50">
                Next
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    data: {
        type: Array,
        default: () => []
    },
    headers: {
        type: Array,
        default: () => []
    },
    editable: {
        type: Boolean,
        default: false
    }
});

const searchQuery = ref('');
const currentPage = ref(1);
const recordsPerPage = 10;

// Computed property to sort the data by created_at (most recent first)
const sortedData = computed(() => {
    // Create a shallow copy of the data array and sort by created_at
    return [...props.data].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
});

// Filter data based on search query using the sortedData
const filteredData = computed(() => {
    if (!searchQuery.value) return sortedData.value;
    return sortedData.value.filter(item =>
        props.headers.some(header => {
            const value = item[header.key];
            return value && value.toString().toLowerCase().includes(searchQuery.value.toLowerCase());
        })
    );
});

// Compute total pages based on filtered results
const totalPages = computed(() =>
    Math.ceil(filteredData.value.length / recordsPerPage)
);

// Slice the filtered data into pages
const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * recordsPerPage;
    return filteredData.value.slice(start, start + recordsPerPage);
});

const prevPage = () => {
    if (currentPage.value > 1) currentPage.value--;
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) currentPage.value++;
};

// Reset current page when search query changes
watch(searchQuery, () => {
    currentPage.value = 1;
});
</script>
