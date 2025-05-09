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

                    <Dropdown>
                        <template #trigger>
                            <span class="inline-flex rounded-md">
                                <button type="button"
                                    class="inline-flex items-center rounded-md border border-transparent leading-4 transition duration-150 ease-in-out text-gray-300 hover:text-gray-100">
                                    Prosperity tools

                                    <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </span>
                        </template>

                        <template #content>
                            <DropdownLink :href="route('goal')">
                                Goal Setting
                            </DropdownLink>
                            <DropdownLink :href="route('budget')">
                                Budget Planner
                            </DropdownLink>
                            <DropdownLink :href="route('networth')">
                                Networth calculator
                            </DropdownLink>
                            <DropdownLink :href="route('debt')">
                                Debt Manager
                            </DropdownLink>
                            <DropdownLink :href="route('investment')">
                                Investment Planner
                            </DropdownLink>
                        </template>
                    </Dropdown>


                    <Dropdown>
                        <template #trigger>
                            <span class="inline-flex rounded-md">
                                <button type="button"
                                    class="inline-flex items-center rounded-md border border-transparent leading-4 transition duration-150 ease-in-out text-gray-300 hover:text-gray-100">
                                    Services

                                    <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </span>
                        </template>

                        <template #content>
                            <DropdownLink :href="route('training')">
                                Training
                            </DropdownLink>
                            <DropdownLink :href="route('advisory')">
                                Advisory
                            </DropdownLink>
                            <DropdownLink :href="route('business.support')">
                                Business Support
                            </DropdownLink>
                        </template>
                    </Dropdown>
                    <Link :href="route('books')" class="text-gray-300 hover:text-gray-100">Buy Book</Link>
                    <Link :href="route('blogs')" class="text-gray-300 hover:text-gray-100">Blogs</Link>
                    <Link :href="route('feedback')" class="text-gray-300 hover:text-gray-100">Feedback</Link>
                    <Dropdown v-if="$page.props.auth.user" align="right" width="48">
                        <template #trigger>
                            <span class="inline-flex rounded-md">
                                <button type="button"
                                    class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                                    {{ $page.props.auth.user.name }}

                                    <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </span>
                        </template>

                        <template #content>
                            <DropdownLink :href="route('budget.index')">
                                Dashboard
                            </DropdownLink>
                            <DropdownLink :href="route('profile.edit')">
                                Profile
                            </DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </DropdownLink>
                        </template>
                    </Dropdown>
                    <Link v-if="!$page.props.auth.user" :href="route('login')"
                        class="bg-white text-gray-900 block px-3 py-2 rounded-md">Log
                    in</Link>
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
                <Dropdown>
                    <template #trigger>
                        <span class="inline-flex rounded-md">
                            <button type="button"
                                class="inline-flex items-center rounded-md border border-transparent leading-4 transition duration-150 ease-in-out text-gray-300 hover:text-gray-100">
                                Prosperity tools

                                <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </span>
                    </template>

                    <template #content>
                        <DropdownLink :href="route('goal')">
                            Goal Setting
                        </DropdownLink>
                        <DropdownLink :href="route('budget')">
                            Budget Planner
                        </DropdownLink>
                        <DropdownLink :href="route('networth')">
                            Networth calculator
                        </DropdownLink>
                        <DropdownLink :href="route('debt')">
                            Debt Manager
                        </DropdownLink>
                        <DropdownLink :href="route('investment')">
                            Investment Planner
                        </DropdownLink>
                    </template>
                </Dropdown>
                <Dropdown>
                    <template #trigger>
                        <span class="inline-flex rounded-md">
                            <button type="button"
                                class="inline-flex items-center rounded-md border border-transparent leading-4 transition duration-150 ease-in-out text-gray-300 hover:text-gray-100">
                                Services

                                <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </span>
                    </template>

                    <template #content>
                        <DropdownLink :href="route('training')">
                            Training
                        </DropdownLink>
                        <DropdownLink :href="route('advisory')">
                            Advisory
                        </DropdownLink>
                        <DropdownLink :href="route('business-support')">
                            Business Support
                        </DropdownLink>
                    </template>
                </Dropdown>
                <Link :href="route('books')" class="text-gray-300 hover:text-white block px-3 py-2">Buy Book</Link>
                <Link :href="route('blogs')" class="text-gray-300 hover:text-white block px-3 py-2">Blogs</Link>
                <Link :href="route('feedback')" class="text-gray-300 hover:text-white block px-3 py-2">Feedback</Link>
                <Dropdown v-if="$page.props.auth.user" align="right" width="48">
                    <template #trigger>
                        <span class="inline-flex rounded-md">
                            <button type="button"
                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                                {{ $page.props.auth.user.name }}

                                <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </span>
                    </template>

                    <template #content>
                        <DropdownLink :href="route('budget.index')">
                            Dashboard
                        </DropdownLink>
                        <DropdownLink :href="route('profile.edit')">
                            Profile
                        </DropdownLink>
                        <DropdownLink :href="route('logout')" method="post" as="button">
                            Log Out
                        </DropdownLink>
                    </template>
                </Dropdown>
                <Link v-if="!$page.props.auth.user" :href="route('login')"
                    class="bg-white text-gray-900 block px-3 py-2 rounded-md">Log in</Link>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'

const isScrolled = ref(false)
const isMobileMenuOpen = ref(false)

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

onMounted(() => {
    handleScroll()
    window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
})
</script>
