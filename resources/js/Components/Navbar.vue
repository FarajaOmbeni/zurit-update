<template>
    <nav :class="[
        'fixed w-full top-0 z-50 transition-all duration-300',
        isHomePage()
            ? (isScrolled ? 'bg-purple-900 shadow-lg' : 'bg-transparent')
            : 'bg-purple-900'
    ]">
        <div class="max-w-8xl mx-auto px-4 md:px-6">
            <div class="flex items-center justify-between h-24">
                <!-- Logo -->
                <div class="flex-shrink-0 py-2">
                    <Link href="/" class="block">
                    <img src="/images/home/zurit.png" class="w-36" alt="Zurit Logo" />
                    </Link>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <Link href="/" class="text-yellow-500 hover:text-yellow-400">Home</Link>
                    <Link href="/about" class="text-gray-300 hover:text-gray-100">About us</Link>

                    <!-- Prosperity Tools Dropdown -->
                    <div class="relative">
                        <button @click="toggleDropdown('prosperityTools')"
                            class="inline-flex items-center text-gray-300 hover:text-gray-100 focus:outline-none">
                            Prosperity Dashboard
                            <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div v-if="activeDropdown === 'prosperityTools'"
                            class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1"
                            @click.outside="closeDropdowns">
                            <Link v-for="item in prosperityTools" :key="item.href" :href="item.href"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            {{ item.name }}
                            </Link>
                        </div>
                    </div>

                    <!-- Services Dropdown -->
                    <div class="relative">
                        <button @click="toggleDropdown('services')"
                            class="inline-flex items-center text-gray-300 hover:text-gray-100 focus:outline-none">
                            Services
                            <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div v-if="activeDropdown === 'services'"
                            class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1"
                            @click.outside="closeDropdowns">
                            <Link v-for="item in services" :key="item.href" :href="item.href"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            {{ item.name }}
                            </Link>
                        </div>
                    </div>

                    <Link href="/books" class="text-gray-300 hover:text-gray-100">Buy Book</Link>
                    <Link href="/blogs" class="text-gray-300 hover:text-gray-100">Blogs</Link>
                    <Link href="/feedback" class="text-gray-300 hover:text-gray-100">Feedback</Link>

                    <!-- User Menu -->
                    <div v-if="user" class="relative">
                        <button @click="toggleDropdown('user')"
                            class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition hover:text-gray-700 focus:outline-none">
                            {{ user.name }}
                            <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div v-if="activeDropdown === 'user'"
                            class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1"
                            @click.outside="closeDropdowns">
                            <!-- Show Coach Dashboard if user is coach -->
                            <template v-if="user.role === 2">
                                <Link href="/coach" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Coach Dashboard
                                </Link>
                            </template>
                            <!-- Show Admin Dashboard if user is admin -->
                            <template v-if="user.role === 1">
                                <Link href="/admin" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Admin Dashboard
                                </Link>
                            </template>
                            <Link v-for="item in userMenuItems" :key="item.href" :href="item.href"
                                :method="item.href === '/logout' ? 'post' : 'get'"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            {{ item.name }}
                            </Link>
                        </div>
                    </div>
                    <Link v-else :href="route('login')"
                        class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-900 hover:bg-gray-100">
                    Log in
                    </Link>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="isMobileMenuOpen = !isMobileMenuOpen"
                    class="md:hidden text-gray-300 hover:text-white focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="!isMobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Slide-in Panel -->
        <transition name="slide">
            <div v-if="isMobileMenuOpen"
                class="fixed inset-y-0 right-0 w-[300px] bg-purple-900 shadow-xl z-50 md:hidden overflow-y-auto">
                <div class="flex justify-between items-center p-4 border-b border-purple-800">
                    <Link href="/" @click="isMobileMenuOpen = false">
                    <img src="/images/home/zurit.png" class="w-28" alt="Zurit Logo" />
                    </Link>
                    <button @click="isMobileMenuOpen = false" class="text-gray-300 hover:text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="py-4">
                    <div class="space-y-1 px-2">
                        <Link href="/" class="block py-3 px-4 text-yellow-500 hover:bg-purple-800 rounded-md"
                            @click="isMobileMenuOpen = false">
                        Home
                        </Link>
                        <Link href="/about"
                            class="block py-3 px-4 text-gray-300 hover:text-white hover:bg-purple-800 rounded-md"
                            @click="isMobileMenuOpen = false">
                        About us
                        </Link>

                        <!-- Mobile Accordions for Dropdowns -->
                        <div class="border-none">
                            <button @click="toggleMobileAccordion('prosperityTools')"
                                class="flex w-full justify-between py-3 px-4 text-gray-300 hover:text-white hover:bg-purple-800 rounded-md">
                                Prosperity tools
                                <svg :class="[
                                    'h-4 w-4 transition-transform',
                                    activeMobileAccordion === 'prosperityTools' ? 'rotate-180' : ''
                                ]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div v-if="activeMobileAccordion === 'prosperityTools'" class="pl-4 space-y-1 mt-1">
                                <Link v-for="item in prosperityTools" :key="item.href" :href="item.href"
                                    class="block py-2 px-4 text-gray-300 hover:text-white hover:bg-purple-800 rounded-md"
                                    @click="isMobileMenuOpen = false">
                                {{ item.name }}
                                </Link>
                            </div>
                        </div>

                        <div class="border-none">
                            <button @click="toggleMobileAccordion('services')"
                                class="flex w-full justify-between py-3 px-4 text-gray-300 hover:text-white hover:bg-purple-800 rounded-md">
                                Services
                                <svg :class="[
                                    'h-4 w-4 transition-transform',
                                    activeMobileAccordion === 'services' ? 'rotate-180' : ''
                                ]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div v-if="activeMobileAccordion === 'services'" class="pl-4 space-y-1 mt-1">
                                <Link v-for="item in services" :key="item.href" :href="item.href"
                                    class="block py-2 px-4 text-gray-300 hover:text-white hover:bg-purple-800 rounded-md"
                                    @click="isMobileMenuOpen = false">
                                {{ item.name }}
                                </Link>
                            </div>
                        </div>

                        <Link href="/books"
                            class="block py-3 px-4 text-gray-300 hover:text-white hover:bg-purple-800 rounded-md"
                            @click="isMobileMenuOpen = false">
                        Buy Book
                        </Link>
                        <Link href="/blogs"
                            class="block py-3 px-4 text-gray-300 hover:text-white hover:bg-purple-800 rounded-md"
                            @click="isMobileMenuOpen = false">
                        Blogs
                        </Link>
                        <Link href="/feedback"
                            class="block py-3 px-4 text-gray-300 hover:text-white hover:bg-purple-800 rounded-md"
                            @click="isMobileMenuOpen = false">
                        Feedback
                        </Link>

                        <!-- User Menu for Mobile -->
                        <div v-if="user" class="border-none mt-2">
                            <button @click="toggleMobileAccordion('user')"
                                class="flex w-full justify-between py-3 px-4 text-white bg-white/10 hover:bg-white/20 rounded-md">
                                {{ user.name }}
                                <svg :class="[
                                    'h-4 w-4 transition-transform',
                                    activeMobileAccordion === 'user' ? 'rotate-180' : ''
                                ]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div v-if="activeMobileAccordion === 'user'" class="pl-4 space-y-1 mt-1">
                                <!-- Show Coach Dashboard if user is coach -->
                                <template v-if="user.role === 2">
                                    <Link href="/coach"
                                        class="block py-2 px-4 text-gray-300 hover:text-white hover:bg-purple-800 rounded-md">
                                    Coach Dashboard
                                    </Link>
                                </template>
                                <!-- Show Admin Dashboard if user is admin -->
                                <template v-if="user.role === 1">
                                    <Link href="/admin"
                                        class="block py-2 px-4 text-gray-300 hover:text-white hover:bg-purple-800 rounded-md">
                                    Admin Dashboard
                                    </Link>
                                </template>
                                <Link v-for="item in userMenuItems" :key="item.href" :href="item.href"
                                    :method="item.href === '/logout' ? 'post' : 'get'"
                                    class="block py-2 px-4 text-gray-300 hover:text-white hover:bg-purple-800 rounded-md"
                                    @click="isMobileMenuOpen = false">
                                {{ item.name }}
                                </Link>
                            </div>
                        </div>
                        <Link v-else href="/login"
                            class="block py-3 px-4 mt-4 text-center bg-white text-purple-900 hover:bg-gray-100 rounded-md"
                            @click="isMobileMenuOpen = false">
                        Log in
                        </Link>
                    </div>
                </div>
            </div>
        </transition>
    </nav>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted, computed } from 'vue'

/* -------------------------------------------------
   1.  Get the global Inertia page object
   2.  Derive the current user from $page.props.auth.user
-------------------------------------------------- */
const page = usePage()
const user = computed(() => page.props.auth?.user ?? null)

/* -------------------------------------------------
   Local state
-------------------------------------------------- */
const isScrolled = ref(false)
const isMobileMenuOpen = ref(false)
const activeDropdown = ref(null)
const activeMobileAccordion = ref(null)

/* -------------------------------------------------
   Static nav data
-------------------------------------------------- */
const prosperityTools = [
    { name: 'Goal Setting', href: '/goal-setting' },
    { name: 'Budget Planner', href: '/budget-planner' },
    { name: 'Networth calculator', href: '/networth-calculator' },
    { name: 'Debt Manager', href: '/debt-manager' },
    { name: 'Investment Planner', href: '/investment-planner' },
    { name: 'Zuriscore', href: '/zuriscore' },
    { name: 'Calculators', href: '/calculators' },
    { name: 'Questionnaires', href: '/questionnaires' },
]

const services = [
    { name: 'Training', href: '/training' },
    { name: 'Advisory', href: '/advisory' },
    { name: 'Business Support', href: '/business-support' },
]

const userMenuItems = [
    { name: 'Prosperity Dashboard', href: '/user/budget' },
    { name: 'Profile', href: '/profile' },
    { name: 'Log Out', href: '/logout' }
]

/* -------------------------------------------------
   Helpers
-------------------------------------------------- */
const handleScroll = () => { isScrolled.value = window.scrollY > 10 }

const toggleDropdown = name => { activeDropdown.value = activeDropdown.value === name ? null : name }
const closeDropdowns = () => { activeDropdown.value = null }
const toggleMobileAccordion = name => { activeMobileAccordion.value = activeMobileAccordion.value === name ? null : name }

const isHomePage = () => window.location.pathname === '/'

/* -------------------------------------------------
   Lifecycle
-------------------------------------------------- */
onMounted(() => {
    handleScroll()
    window.addEventListener('scroll', handleScroll)
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) isMobileMenuOpen.value = false
    })
})

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
})
</script>


<style scoped>
/* Slide animation for mobile menu */
.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s ease;
}

.slide-enter-from,
.slide-leave-to {
    transform: translateX(100%);
}

.slide-enter-to,
.slide-leave-from {
    transform: translateX(0);
}
</style>