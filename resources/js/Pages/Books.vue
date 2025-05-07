<template>

    <Head title="Books" />
    <Navbar />
    <div class="mt-12 p-16">
        <h2 class="text-3xl mb-4 lg:text-4xl font-bold text-yellow-600">Books</h2>
        <div class="flex flex-col flex-wrap md:flex-row items-center md:items-start justify-center gap-12">
            <BookCard :book="{
                title: 'Thrive',
                description: 'Your ultimate guide to financial freedom. Clear, concise steps to wealth creation.',
                price: 1500,
                image: '/images/books/thrive.webp'
            }" />
            <BookCard :book="{
                title: 'Financial Goal Setting',
                description: 'Unlock the path to realizing your financial deams with this insightful guide. Dive into the crucial art of goal setting, guided by the wheel of life, to pinpoint your aspirations',
                price: 350,
                image: '/images/books/goal_setting.webp'
            }" />
            <BookCard :book="{
                title: 'Transcend',
                description: 'Navigating the Realities - Beyond the allure of Entrepreneurship',
                price: 1800,
                image: '/images/books/transcend.jpg'
            }" />

            <BookCard :book="{
                title: 'From Earning to Empowerment',
                description: 'A guide to financial wellness',
                price: 350,
                image: '/images/books/earning.png'
            }" />
        </div>
    </div>
    <Footer />
</template>

<script setup>
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import BookCard from '@/Components/Shared/BookCard.vue';
import { Head } from '@inertiajs/vue3';
</script>