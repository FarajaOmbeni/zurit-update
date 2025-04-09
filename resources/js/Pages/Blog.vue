<template>

    <Head title="Blogs" />
    <Navbar />
    <div class="mt-32 mb-24  mx-4 ">
        <h1
            class="text-3xl md:text-4xl lg:text-5xl xl:text-6xl md:text-start font-bold text-purple-500 mb-4 text-center">
            Our <span class="text-yellow-500">Blogs</span></h1>
        <div class="flex flex-col md:flex-row items-center md:items-start justify-center gap-12">
            <!-- Blog Cards Section -->
            <div class="grid grid-cols-1 w-[70%] gap-8">
                <BlogCard :image="`/storage/blogs/${blog.blog_image}`" :title="blog.blog_title"
                    :content="blog.blog_message" :link="blog.link" :date="formatDate(blog.created_at)" />
            </div>
            <!-- Search and Recent Posts Section -->
            <div class="w-[30%]">
                <div class="flex gap-4">
                    <input class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-purple-500" type="text"
                        placeholder="Search blogs..." />
                    <button
                        class="px-4 py-2 rounded-xl bg-gradient-to-r from-purple-700 to-yellow-500 text-white font-medium transition-all duration-300 ease-in-out hover:bg-gradient-to-r hover:from-yellow-500 hover:to-purple-700">
                        Search
                    </button>
                </div>
                <div class="">
                    <h1 class="text-xl font-bold text-gray-800">Recent Posts</h1>
                    <ul class="mt-2 pl-0 space-y-4 list-none">
                        <li v-for="blog in blogs.slice(0, 5)"
                            class="text-purple-700 hover:font-bold hover:text-purple-900 transition-colors duration-300">
                            <a :href="`/blog/${blog.id}`">
                                <div class="flex gap-2 items-center cursor-pointer">
                                    <img class="w-[6rem] h-20" :src="`/storage/blogs/${blog.blog_image}`"
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
    </div>
    <Footer />
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { defineProps } from 'vue'
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import BlogCard from '@/Components/Shared/BlogCard.vue';
import { formatDate } from '@/Components/Composables/useDateFormat';

const props = defineProps({
    blogs: Array,
    blog: Object
})
const blog = props.blog
const blogs = props.blogs
console.log("BLOG: ", blog.blog_image)
</script>

<style>
h2 {
    display: block;
    font-size: 2em;
    margin-top: 0.67em;
    margin-bottom: 0.67em;
    margin-left: 0;
    margin-right: 0;
    font-weight: bold;
}

h3 {
    display: block;
    font-size: 1.5em;
    margin-top: 0.83em;
    margin-bottom: 0.83em;
    margin-left: 0;
    margin-right: 0;
    font-weight: bold;
}

h4 {
    display: block;
    font-size: 1.17em;
    margin-top: 1em;
    margin-bottom: 1em;
    margin-left: 0;
    margin-right: 0;
    font-weight: bold;
}

ul {
    display: block;
    list-style-type: disc;
    margin-top: 1em;
    margin-bottom: 1 em;
    margin-left: 0;
    margin-right: 0;
    padding-left: 40px;
}

li {
    display: list-item;
}

ol {
    display: block;
    list-style-type: lower-roman;
    margin-top: 1em;
    margin-bottom: 1em;
    margin-left: 0;
    margin-right: 0;
    padding-left: 40px;
}
</style>
