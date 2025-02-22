<template>
    <nav :class="[
        'fixed w-full top-0 z-50 transition-all duration-300',
        isScrolled ? 'bg-purple-900 shadow-lg' : 'bg-transparent'
    ]">
        <div class="max-w-8xl mx-auto px-4 md:px-6">
            <div class="flex items-center justify-between h-24">
                <!-- Logo -->
                <div class="flex-shrink-0 py-2">
                    <Link :href="route('home')"><img src="/images/home/zurit.png" class="w-36" alt="Zurit Logo" />
                    </Link>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <Link :href="route('home')" class="text-yellow-500 hover:text-yellow-400">Home</Link>
                    <Link :href="route('about')" class="text-gray-300 hover:text-gray-100">About us</Link>

                    <!-- Updated Prosperity Tools Dropdown -->
                    <div class="relative group" @mouseenter="isDropdownOpen = true" @mouseleave="hideDropdown">
                        <button class="text-gray-300 hover:text-gray-100 flex items-center">
                            Prosperity Tools
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div v-show="isDropdownOpen"
                            class="absolute left-0 mt-2 w-48 bg-purple-900 shadow-lg rounded-md overflow-hidden"
                            @mouseenter="isDropdownOpen = true" @mouseleave="isDropdownOpen = false">
                            <Link :href="route('goal')" class="block px-4 py-2 text-gray-300 hover:bg-purple-700">Goal Setting</Link>
                            <Link :href="route('budget')" class="block px-4 py-2 text-gray-300 hover:bg-purple-700">Budget Planner</Link>
                            <Link :href="route('networth')" class="block px-4 py-2 text-gray-300 hover:bg-purple-700">Net Worth
                                Calculator</Link>
                            <Link :href="route('debt')" class="block px-4 py-2 text-gray-300 hover:bg-purple-700">Debt Manager</Link>
                            <Link :href="route('investment')" class="block px-4 py-2 text-gray-300 hover:bg-purple-700">Investment Planner</Link>
                        </div>
                    </div>


                    <a :href="route('training')" class="text-gray-300 hover:text-gray-100">Services</a>
                    <a href="#" class="text-gray-300 hover:text-gray-100">Buy Book</a>
                    <a href="#" class="text-gray-300 hover:text-gray-100">Blogs</a>
                    <a href="#" class="text-gray-300 hover:text-gray-100">Feedback</a>
                    <Link :href="route('login')" class="bg-white text-gray-900 px-4 py-2 rounded-md hover:bg-gray-300">
                    Join Us
                    </Link>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button @click="toggleMobileMenu" class="text-gray-300 hover:text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="!isMobileMenuOpen" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div v-if="isMobileMenuOpen" class="md:hidden bg-[#0B1A24] float-right">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <Link :href="route('home')" class="text-yellow-500 block px-3 py-2">Home</Link>
                <Link :href="route('about')" class="text-gray-300 hover:text-white block px-3 py-2">About us</Link>
                <div class="relative">
                    <button @click="toggleDropdown" class="text-gray-300 hover:text-white block px-3 py-2">
                        Prosperity Tools
                        <svg class="ml-1 h-4 w-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div v-if="isDropdownOpen" class="bg-purple-900 shadow-lg rounded-md overflow-hidden mt-2">
                        <Link :href="route('goal')" class="block px-4 py-2 text-gray-300 hover:bg-purple-700">Goal Setting</Link>
                        <Link :href="route('budget')" class="block px-4 py-2 text-gray-300 hover:bg-purple-700">Budget Planner</Link>
                        <Link :href="route('networth')" class="block px-4 py-2 text-gray-300 hover:bg-purple-700">Net Worth Calculator</Link>
                        <Link :href="route('debt')" class="block px-4 py-2 text-gray-300 hover:bg-purple-700">Debt Manager</Link>
                        <Link :href="route('investment')" class="block px-4 py-2 text-gray-300 hover:bg-purple-700">Investment Planner</Link>
                    </div>
                </div>
                <a :href="route('training')" class="text-gray-300 hover:text-white block px-3 py-2">Services</a>
                <a href="#" class="text-gray-300 hover:text-white block px-3 py-2">Buy Book</a>
                <a href="#" class="text-gray-300 hover:text-white block px-3 py-2">Blogs</a>
                <a href="#" class="text-gray-300 hover:text-white block px-3 py-2">Feedback</a>
                <a href="#" class="bg-white text-gray-900 block px-3 py-2 rounded-md">Join Us</a>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'

const isScrolled = ref(false)
const isMobileMenuOpen = ref(false)
const isDropdownOpen = ref(false)

const page = usePage()

const isHomePage = computed(() => page.url === '/')

const handleScroll = () => {
    if (isHomePage.value) {
        isScrolled.value = window.scrollY > 50
    } else {
        isScrolled.value = true // Force solid color on other pages
    }
}

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value
}

onMounted(() => {
    handleScroll() // Apply initial state based on page
    window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
})
</script>
