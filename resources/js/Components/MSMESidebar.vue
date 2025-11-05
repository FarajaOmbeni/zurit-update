<template>
    <div class="flex flex-col h-screen overflow-x-hidden">
        <!-- Sidebar -->
        <div
            :class="[
                'md:block hidden transition-all duration-300 bg-purple-800 text-white fixed h-full z-10',
                sidebarOpen ? 'w-64' : 'w-16',
                'md:left-0',
                'left-auto right-0',
            ]"
        >
            <!-- Toggle Button -->
            <button
                @click="toggleSidebar"
                :class="[
                    'absolute top-5 bg-yellow-400 hover:bg-yellow-500 rounded-full p-1 shadow-lg',
                    'hidden',
                    'md:block',
                    'transition-transform',
                    'md:-right-3',
                ]"
            >
                <svg
                    :class="[
                        'h-5 w-5 text-purple-800 transition-transform',
                        !sidebarOpen ? 'transform rotate-180' : '',
                    ]"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>
            </button>

            <!-- Logo -->
            <div
                class="flex items-center justify-center h-16 border-b border-purple-700"
            >
                <div
                    v-if="sidebarOpen"
                    class="text-xl font-bold text-yellow-400"
                >
                    <Link :href="route('home')"
                        ><img
                            class="object-cover w-40 h-14"
                            src="/images/home/zurit.png"
                            alt=""
                    /></Link>
                </div>
                <div
                    v-else
                    class="text-xs font-bold text-yellow-400 hidden md:block"
                >
                    <Link :href="route('home')">Zurit</Link>
                </div>

                <div
                    @click="toggleSidebar"
                    class="md:hidden cursor-pointer absolute right-4"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="size-6 text-yellow-400"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5"
                        />
                    </svg>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="mt-6">
                <div
                    v-for="(item, index) in menuItems"
                    :key="index"
                    class="px-4 py-2"
                >
                    <div class="relative group">
                        <Link
                            :href="route(item.link)"
                            class="flex items-center py-2 px-2 rounded hover:bg-purple-700 transition-colors"
                            :class="item.active ? 'bg-purple-700' : ''"
                        >
                            <span class="text-yellow-400">
                                <component
                                    :is="iconMap[item.icon]"
                                    class="h-5 w-5"
                                />
                            </span>
                            <span
                                v-if="sidebarOpen"
                                class="ml-3 whitespace-nowrap"
                                >{{ item.title }}</span
                            >
                        </Link>

                        <!-- Tooltip -->
                        <div
                            v-if="!sidebarOpen"
                            class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 whitespace-nowrap z-50 top-1/2 transform -translate-y-1/2"
                        >
                            {{ item.title }}
                            <div
                                class="absolute right-full top-1/2 transform -translate-y-1/2 border-4 border-transparent border-r-gray-900"
                            ></div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- User Profile -->
            <div
                class="absolute bottom-0 w-full border-t border-purple-700 p-2"
            >
                <div class="flex items-center px-2 py-3">
                    <div
                        @click.stop="toggleDropdown"
                        class="flex-shrink-0 h-8 w-8 rounded-full bg-white border-2 border-yellow-400"
                    ></div>
                    <div v-if="sidebarOpen" class="ml-3 relative">
                        <div
                            @click.stop="toggleDropdown"
                            class="cursor-pointer group"
                        >
                            <p
                                class="text-sm font-medium group-hover:text-yellow-500"
                            >
                                {{ $page.props.auth.user.name }}
                            </p>
                            <p
                                class="text-xs text-purple-300 group-hover:text-yellow-500"
                            >
                                {{ $page.props.auth.user.email }}
                            </p>
                        </div>

                        <!-- Dropdown menu opening upwards -->
                        <div
                            v-if="dropdownOpen"
                            class="absolute bottom-full left-0 mb-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
                        >
                            <Link
                                :href="route('profile.edit')"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-200"
                                >Profile</Link
                            >
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-purple-200"
                                >Logout</Link
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content - Adjust padding based on sidebar position -->
        <div
            :class="[
                'flex-1 transition-all duration-300',
                'md:ml-16 md:mr-0',
                sidebarOpen ? 'md:ml-64' : 'md:ml-16',
                'ml-0 mr-0',
            ]"
        >
            <!-- Bottom Navigation for Mobile -->
            <nav
                class="fixed bottom-0 left-0 right-0 z-50 bg-purple-800 border-t border-purple-700 text-white flex md:hidden"
            >
                <div
                    v-for="(item, index) in menuItems.slice(0, 5)"
                    :key="index"
                    class="flex-1 flex flex-col items-center justify-center py-2"
                >
                    <Link
                        :href="route(item.link)"
                        class="flex flex-col items-center text-xs"
                        :class="item.active ? 'text-yellow-400' : 'text-white'"
                    >
                        <component
                            :is="iconMap[item.icon]"
                            class="w-5 h-5 mb-1"
                        />
                        <span>{{ item.title.split(" ")[0] }}</span>
                    </Link>
                </div>

                <!-- Hamburger menu trigger -->
                <div
                    class="flex-1 flex flex-col items-center justify-center py-2"
                >
                    <button
                        @click="toggleMobileMenu"
                        class="flex flex-col items-center text-xs text-white"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-5 h-5 mb-1 text-yellow-400"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5"
                            />
                        </svg>
                        <span>More</span>
                    </button>
                </div>
            </nav>

            <!-- Mobile Slide-in Menu -->
            <transition name="slide">
                <div
                    v-if="mobileMenuOpen"
                    class="fixed top-0 bottom-0 right-0 w-1/2 bg-purple-800 text-white z-50 shadow-lg transform transition-transform duration-300 md:hidden"
                >
                    <!-- Header with Close Button -->
                    <div
                        class="flex justify-between items-center px-4 py-3 border-b border-purple-700"
                    >
                        <h2 class="text-lg font-semibold">Zurit Consulting</h2>
                        <button
                            @click="toggleMobileMenu"
                            class="text-white hover:text-yellow-400"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-6 h-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>

                    <!-- Menu Items -->
                    <div class="p-4 space-y-4 text-sm">
                        <Link
                            v-for="(item, index) in extendedMenuItems"
                            :key="index"
                            :href="route(item.link)"
                            @click="toggleMobileMenu"
                            :class="[
                                'flex items-center space-x-2',
                                item.active
                                    ? 'text-yellow-400 font-semibold'
                                    : 'text-white hover:text-yellow-400',
                            ]"
                        >
                            <component
                                :is="iconMap[item.icon]"
                                class="w-4 h-4"
                            />
                            <span>{{ item.title }}</span>
                        </Link>
                    </div>
                </div>
            </transition>

            <div class="p-6 rounded-lg bg-gray-100 overflow-auto">
                <h1 class="text-2xl font-semibold text-purple-800">
                    {{ title }}
                </h1>
                <div
                    class="lg:p-6 rounded-lg bg-gray-100 overflow-auto pb-20 md:pb-6"
                >
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted } from "vue";
import {
    HomeIcon,
    ChartBarIcon,
    DocumentTextIcon,
    CurrencyDollarIcon,
    BuildingStorefrontIcon,
    CalculatorIcon,
    ClipboardDocumentListIcon,
    CalendarDaysIcon,
} from "@heroicons/vue/24/outline";

const iconMap = {
    HomeIcon,
    ChartBarIcon,
    DocumentTextIcon,
    CurrencyDollarIcon,
    BuildingStorefrontIcon,
    CalculatorIcon,
    ClipboardDocumentListIcon,
    CalendarDaysIcon,
};

defineProps({
    title: String,
});

const dropdownOpen = ref(false);
const page = usePage();
const currentRoute = page.url;

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
};

const closeDropdown = () => {
    dropdownOpen.value = false;
};

onMounted(() => {
    document.addEventListener("click", closeDropdown);
});

onUnmounted(() => {
    document.removeEventListener("click", closeDropdown);
});

// MSME-specific menu items
const menuItems = [
    {
        title: "Home",
        icon: "HomeIcon",
        active: currentRoute.startsWith("/home"),
        link: "home",
    },
    {
        title: "MSME",
        icon: "BuildingStorefrontIcon",
        active: currentRoute.startsWith("/msme/dashboard"),
        link: "msme.dashboard",
    },
    {
        title: "Record Transaction",
        icon: "ChartBarIcon",
        active: currentRoute.startsWith("/msme/cashflow"),
        link: "cashflow.index",
    },
    {
        title: "Price Product/Service",
        icon: "CalculatorIcon",
        // Active only on the create page
        active: currentRoute.startsWith("/msme/pricing/create"),
        link: "pricing.create",
    },
    {
        title: "Pricing Models",
        icon: "CurrencyDollarIcon",
        // Active on pricing index and any non-create pricing routes (show/edit/etc)
        active:
            currentRoute.startsWith("/msme/pricing") &&
            !currentRoute.startsWith("/msme/pricing/create"),
        link: "pricing.index",
    },
    {
        title: "Generate Report",
        icon: "ClipboardDocumentListIcon",
        active: currentRoute.startsWith("/msme/reports"),
        link: "msme.reports",
    },
    {
        title: "Month-End Closing",
        icon: "CalendarDaysIcon",
        active: currentRoute.startsWith("/msme/month-end"),
        link: "month-end.index",
    },
];

const extendedMenuItems = menuItems;

const sidebarOpen = ref(false);
const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const mobileMenuOpen = ref(false);
const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
};
</script>

<style>
.slide-enter-from,
.slide-leave-to {
    transform: translateX(100%);
}

.slide-enter-to,
.slide-leave-from {
    transform: translateX(0%);
}

.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s ease;
}
</style>
