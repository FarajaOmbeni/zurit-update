<template>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div :class="[
            'transition-all duration-300 bg-purple-800 text-white fixed h-full z-10',
            sidebarOpen ? 'w-64' : 'w-16',
            'md:left-0',
            'left-auto right-0'
        ]">
            <!-- Toggle Button -->
            <button @click="toggleSidebar" :class="[
                'absolute top-5 bg-yellow-400 hover:bg-yellow-500 rounded-full p-1 shadow-lg',
                'hidden',
                'md:block',
                'transition-transform',
                'md:-right-3',
            ]">
                <svg :class="[
                    'h-5 w-5 text-purple-800 transition-transform',
                    !sidebarOpen ? 'transform rotate-180' : ''
                ]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <!-- Logo -->
            <div class="flex items-center justify-center h-16 border-b border-purple-700">
                <div v-if="sidebarOpen" class="text-xl font-bold text-yellow-400">
                    <Link :href="route('home')"><img class="object-cover w-30 h-8" src="/images/home/zurit.svg" alt="">
                    </Link>
                </div>
                <div v-else class="text-xs font-bold text-yellow-400 hidden md:block">
                    <Link :href="route('home')">Zurit</Link>
                </div>

                <div @click="toggleSidebar" class="md:hidden cursor-pointer absolute right-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 text-yellow-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5" />
                    </svg>
                </div>

            </div>

            <!-- Navigation Links -->
            <nav class="mt-6 text-sm">
                <div v-for="(item, index) in visibleMenuItems" :key="index" class="px-4 py-2">
                    <div class="relative group">
                        <Link :href="route(item.link)"
                            class="flex items-center py-2 px-2 rounded hover:bg-purple-700 transition-colors"
                            :class="item.active ? 'bg-purple-700' : ''">
                        <span class="text-yellow-400">
                            <component :is="iconMap[item.icon]" class="h-5 w-5" />
                        </span>
                        <span v-if="sidebarOpen" class="ml-3 whitespace-nowrap">{{ item.title }}</span>
                        </Link>

                        <!-- Tooltip for collapsed sidebar -->
                        <div v-if="!sidebarOpen"
                            class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 whitespace-nowrap z-50 top-1/2 transform -translate-y-1/2">
                            {{ item.title }}
                            <div
                                class="absolute right-full top-1/2 transform -translate-y-1/2 border-4 border-transparent border-r-gray-900">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- More Button as 8th item -->
                <div v-if="hiddenMenuItems.length" class="px-4 py-2">
                    <div class="relative" @click.stop>
                        <button @click="toggleMoreMenu"
                            class="flex items-center py-2 px-2 rounded hover:bg-purple-700 transition-colors w-full">
                            <span class="text-yellow-400">
                                <component :is="iconMap['EllipsisHorizontalIcon']" class="h-5 w-5" />
                            </span>
                            <span v-if="sidebarOpen" class="ml-3 whitespace-nowrap">More</span>
                        </button>

                        <div v-if="moreMenuOpen" :class="[
                            'absolute left-0 bottom-full mb-2',
                            'w-56 bg-white rounded-md shadow-lg py-1 z-50'
                        ]">
                            <Link v-for="(item, index) in hiddenMenuItems" :key="index" :href="route(item.link)"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-200"
                                @click="moreMenuOpen = false">
                            {{ item.title }}
                            </Link>

                            <!-- Course management links inside More -->
                            <div class="my-1 border-t border-gray-200"></div>
                            <Link v-for="(item, index) in courseMoreItems" :key="'cm-' + index" :href="route(item.link)"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-200"
                                @click="moreMenuOpen = false">
                            {{ item.title }}
                            </Link>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- User Profile -->
            <div class="absolute bottom-0 w-full border-t border-purple-700 p-2">
                <div class="flex items-center px-2 py-3">
                    <div @click.stop="toggleDropdown"
                        class="flex-shrink-0 h-8 w-8 rounded-full bg-white border-2 border-yellow-400"></div>
                    <div v-if="sidebarOpen" class="ml-3 relative">
                        <div @click.stop="toggleDropdown" class="cursor-pointer group">
                            <p class="text-sm font-medium group-hover:text-yellow-500">{{ $page.props.auth.user.name }}
                            </p>
                            <p class="text-xs text-purple-300 group-hover:text-yellow-500">{{
                                $page.props.auth.user.email }}</p>
                        </div>

                        <!-- Dropdown menu opening upwards -->
                        <div v-if="dropdownOpen"
                            class="absolute bottom-full left-0 mb-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <Link :href="route('profile.edit')"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-200">
                            Profile
                            </Link>
                            <Link :href="route('logout')" method="post" as="button"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-purple-200">
                            Logout
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content - Adjust padding based on sidebar position -->
        <div :class="[
            'flex-1 transition-all duration-300',
            // Medium screens and up (sidebar on left)
            'md:ml-16 md:mr-0',
            sidebarOpen ? 'md:ml-64' : 'md:ml-16',
            // Small screens (sidebar on right)
            'ml-0 mr-16',
            sidebarOpen ? 'mr-64' : 'mr-16'
        ]">

            <div class="p-6 rounded-lg bg-gray-100 overflow-auto">
                <h1 class="text-2xl font-semibold text-purple-800">{{ title }}</h1>
                <div class="rounded-lg p-6 bg-white text-gray-600">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import {
    UsersIcon,
    UserGroupIcon,
    DocumentTextIcon,
    CalendarDaysIcon,
    CalendarIcon,
    UserIcon,
    ChartBarIcon,
    UserPlusIcon,
    EnvelopeIcon,
    StarIcon,
    ChatBubbleLeftRightIcon,
    VideoCameraIcon,
    EllipsisHorizontalIcon,
} from '@heroicons/vue/24/outline';
import Alert from '@/Components/Shared/Alert.vue';

const iconMap = {
    UsersIcon,
    UserGroupIcon,
    DocumentTextIcon,
    CalendarDaysIcon,
    CalendarIcon,
    UserIcon,
    ChartBarIcon,
    UserPlusIcon,
    EnvelopeIcon,
    StarIcon,
    ChatBubbleLeftRightIcon,
    VideoCameraIcon,
    EllipsisHorizontalIcon,
};

defineProps({
    title: String,
})

const dropdownOpen = ref(false);
const page = usePage();
const currentRoute = page.url;
const moreMenuOpen = ref(false);

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
};

// Close dropdown when clicking outside
const closeDropdown = (e) => {
    dropdownOpen.value = false;
};

// Add event listener when component is mounted
onMounted(() => {
    document.addEventListener('click', closeDropdown);
    document.addEventListener('click', closeMoreMenu);
});

// Clean up event listener when component is unmounted
onUnmounted(() => {
    document.removeEventListener('click', closeDropdown);
    document.removeEventListener('click', closeMoreMenu);
});

// Menu items with icons (using heroicons)
const menuItems = [
    {
        title: 'Users',
        icon: 'UsersIcon',
        active: currentRoute.startsWith('/admin/users'),
        link: 'users.index',
    },
    {
        title: 'Blogs',
        icon: 'DocumentTextIcon',
        icon: 'DocumentTextIcon',
        active: currentRoute.startsWith('/admin/blogs'),
        link: 'blogs.index',
    },
    {
        title: 'Events',
        icon: 'CalendarDaysIcon',
        active: currentRoute.startsWith('/admin/events'),
        link: 'events.index',
    },
    {
        title: 'Coaching',
        icon: 'UserIcon',
        active: currentRoute.startsWith('/admin/coaching'),
        link: 'coaching.index',
    },
    {
        title: 'Revenue Streams',
        icon: 'ChartBarIcon',
        icon: 'ChartBarIcon',
        active: currentRoute.startsWith('/admin/system'),
        link: 'system.index',
    },
    {
        title: 'Add Users',
        icon: 'UserPlusIcon',
        icon: 'UserPlusIcon',
        active: currentRoute.startsWith('/admin/add-users'),
        link: 'add-users.index',
    },
    {
        title: 'Marketing Emails',
        icon: 'EnvelopeIcon',
        icon: 'EnvelopeIcon',
        active: currentRoute.startsWith('/admin/marketing'),
        link: 'marketing.index',
    },
    {
        title: 'Add Testimonials',
        icon: 'StarIcon',
        active: currentRoute.startsWith('/admin/testimonials'),
        link: 'testimonials.index',
    },
    {
        title: 'Manage Videos',
        icon: 'VideoCameraIcon',
        icon: 'VideoCameraIcon',
        active: currentRoute.startsWith('/admin/videos'),
        link: 'videos.index',
    },
]

const visibleMenuItems = menuItems.slice(0, 7)
const hiddenMenuItems = menuItems.slice(7)

const toggleMoreMenu = () => {
    moreMenuOpen.value = !moreMenuOpen.value
}

const closeMoreMenu = () => {
    moreMenuOpen.value = false
}

// Course management items to include in More menu
const courseMoreItems = [
    {
        title: 'Create Main Course',
        link: 'admin.courses.create-main',
    },
    {
        title: 'Add Sub-Course',
        link: 'admin.courses.create',
    },
    {
        title: 'Manage Courses',
        link: 'admin.courses.index',
    },
    {
        title: 'Manage Quizzes',
        link: 'admin.quizzes.index',
    },
    {
        title: 'Course Access',
        link: 'admin.courses.access',
    },
]

// Sidebar state - set to true by default
const sidebarOpen = ref(true);

// Toggle sidebar function
const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};
</script>
