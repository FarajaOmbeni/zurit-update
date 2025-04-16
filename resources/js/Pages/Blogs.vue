<template>

    <Head title="Blogs" />
    <Navbar />
    <div class="mt-32 mb-24 mx-4">
        <h1
            class="text-3xl md:text-4xl lg:text-5xl xl:text-6xl md:text-start font-bold text-purple-500 mb-4 text-center">
            Our <span class="text-yellow-500">Blogs</span>
        </h1>
        <div class="flex flex-col md:flex-row items-center md:items-start justify-center gap-12">
            <!-- Blog Cards Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 w-[70%] gap-8">
                <BlogCard v-for="blog in paginatedBlogs" :key="blog.id" :displaying="true" :clamped="true"
                    :image="`/public/storage/blogs/${blog.blog_image}`" :title="blog.blog_title" :content="blog.blog_message"
                    :link="`/blog/${blog.id}`" :date="formatDate(blog.created_at)" />
                <div v-if="paginatedBlogs.length === 0" class="col-span-2 text-center text-gray-500">
                    No blogs found.
                </div>
            </div>
            <!-- Search and Recent Posts Section -->
            <div class="w-[30%]">
                <div class="flex gap-4 mb-4">
                    <input v-model="searchQuery"
                        class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-purple-500" type="text"
                        placeholder="Search blogs..." />
                    <button @click="searchQuery = ''"
                        class="px-4 py-2 rounded-xl bg-gradient-to-r from-purple-700 to-yellow-500 text-white font-medium transition-all duration-300 ease-in-out hover:bg-gradient-to-r hover:from-yellow-500 hover:to-purple-700">
                        Clear
                    </button>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Recent Posts</h1>
                    <ul class="mt-2 space-y-4 list-none">
                        <li v-for="blog in recentBlogs" :key="blog.id"
                            class="text-purple-700 hover:font-bold hover:text-purple-900 transition-colors duration-300">
                            <a :href="`/blog/${blog.id}`">
                                <div class="flex gap-2 items-center cursor-pointer">
                                    <img class="w-[6rem] h-20" :src="`/public/storage/blogs/${blog.blog_image}`"
                                        alt="Blog image">
                                    <div>
                                        <p class="text-sm">{{ blog.blog_title }}</p>
                                        <div v-html="blog.blog_message" class="line-clamp-2 text-xs"></div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Pagination Controls -->
        <div class="mt-8 flex justify-center items-center gap-4">
            <button @click="prevPage" :disabled="currentPage === 1"
                class="px-4 py-2 rounded-xl bg-gray-200 text-gray-700 disabled:opacity-50">
                Previous
            </button>

            <!-- Numeric pagination buttons -->
            <div class="flex gap-2">
                <button v-for="page in totalPages" :key="page" @click="currentPage = page"
                    :class="{ 'bg-purple-600 text-white': currentPage === page, 'bg-gray-200 text-gray-700': currentPage !== page }"
                    class="px-3 py-2 rounded-xl">
                    {{ page }}
                </button>
            </div>

            <button @click="nextPage" :disabled="currentPage === totalPages"
                class="px-4 py-2 rounded-xl bg-gray-200 text-gray-700 disabled:opacity-50">
                Next
            </button>
        </div>
    </div>
    <Footer />
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import BlogCard from '@/Components/Shared/BlogCard.vue';
import { formatDate } from '@/Components/Composables/useDateFormat';

const props = defineProps({
    blogs: Array
});

// Reverse blogs to show most recent first
const blogs = props.blogs.reverse();

// Search query state
const searchQuery = ref('');

// Filter blogs based on search query matching title or message
const filteredBlogs = computed(() => {
    if (!searchQuery.value.trim()) {
        return blogs;
    }
    const query = searchQuery.value.toLowerCase();
    return blogs.filter(blog =>
        blog.blog_title.toLowerCase().includes(query) ||
        blog.blog_message.toLowerCase().includes(query)
    );
});

// Pagination variables
const currentPage = ref(1);
const blogsPerPage = 8;

// Recalculate pagination when filteredBlogs changes
const totalPages = computed(() => Math.ceil(filteredBlogs.value.length / blogsPerPage));

const paginatedBlogs = computed(() => {
    const start = (currentPage.value - 1) * blogsPerPage;
    return filteredBlogs.value.slice(start, start + blogsPerPage);
});

// Recent posts: using the first 5 from the filtered list for consistency
const recentBlogs = computed(() => filteredBlogs.value.slice(0, 5));

function nextPage() {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
}

function prevPage() {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
}

// Watch the search query so that pagination resets to the first page when search input changes
watch(searchQuery, () => {
    currentPage.value = 1;
});

// console.log("BLOGS: ", blogs);
</script>
