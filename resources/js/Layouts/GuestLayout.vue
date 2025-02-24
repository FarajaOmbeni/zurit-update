<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0 bg-purple-100"
    >
        <div class="">
            <Link href="/">
                <ApplicationLogo class="h-[15rem] w-[15rem] fill-current text-gray-500" />
            </Link>
        </div>

        <div
            class="w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
