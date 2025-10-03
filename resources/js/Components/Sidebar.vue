<template>
  <div class="flex flex-col h-screen overflow-x-hidden">
    <!-- Sidebar -->
    <div :class="[
      'md:block hidden transition-all duration-300 bg-purple-800 text-white fixed h-full z-10',
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
          <Link :href="route('home')"><img class="object-cover w-40 h-14" src="/images/home/zurit.png" alt=""></Link>
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
      <nav class="mt-6">
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

            <!-- Tooltip -->
            <div v-if="!sidebarOpen"
              class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 whitespace-nowrap z-50 top-1/2 transform -translate-y-1/2">
              {{ item.title }}
              <!-- Arrow pointing left -->
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

            <!-- More dropdown: open above the More button -->
            <div v-if="moreMenuOpen" :class="[
              'absolute left-0 bottom-full mb-2',
              'w-48 bg-white rounded-md shadow-lg py-1 z-50'
            ]">
              <Link v-for="(item, index) in hiddenMenuItems" :key="index" :href="route(item.link)"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-200" @click="moreMenuOpen = false">
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
              <p class="text-sm font-medium group-hover:text-yellow-500">{{ $page.props.auth.user.name }}</p>
              <p class="text-xs text-purple-300 group-hover:text-yellow-500">{{ $page.props.auth.user.email }}</p>
            </div>

            <!-- Dropdown menu opening upwards -->
            <div v-if="dropdownOpen"
              class="absolute bottom-full left-0 mb-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
              <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-200">
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
      'md:ml-16 md:mr-0',
      sidebarOpen ? 'md:ml-64' : 'md:ml-16',
      'ml-0 mr-0' // prevent overflow on small screens
    ]">

      <!-- Bottom Navigation for Mobile -->
      <nav
        class="fixed bottom-0 left-0 right-0 z-50 bg-purple-800 border-t border-purple-700 text-white flex md:hidden">
        <!-- First 5 visible items -->
        <div v-for="(item, index) in menuItems.slice(0, 5)" :key="index"
          class="flex-1 flex flex-col items-center justify-center py-2">
          <Link :href="route(item.link)" class="flex flex-col items-center text-xs"
            :class="item.active ? 'text-yellow-400' : 'text-white'">
          <component :is="iconMap[item.icon]" class="w-5 h-5 mb-1" />
          <span>{{ item.title.split(' ')[0] }}</span>
          </Link>
        </div>

        <!-- Hamburger menu trigger -->
        <div class="flex-1 flex flex-col items-center justify-center py-2">
          <button @click="toggleMobileMenu" class="flex flex-col items-center text-xs text-white">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="w-5 h-5 mb-1 text-yellow-400">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5" />
            </svg>
            <span>More</span>
          </button>
        </div>
      </nav>
      <!-- Mobile Slide-in Menu -->
      <transition name="slide">
        <div v-if="mobileMenuOpen"
          class="fixed top-0 bottom-0 right-0 w-1/2 bg-purple-800 text-white z-50 shadow-lg transform transition-transform duration-300 md:hidden">

          <!-- Header with Close Button -->
          <div class="flex justify-between items-center px-4 py-3 border-b border-purple-700">
            <h2 class="text-lg font-semibold">Menu</h2>
            <button @click="toggleMobileMenu" class="text-white hover:text-yellow-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Menu Items -->
          <div class="p-4 space-y-4 text-sm">
            <Link v-for="(item, index) in extendedMenuItems" :key="index" :href="route(item.link)"
              class="flex items-center space-x-2 hover:text-yellow-400">
            <component :is="iconMap[item.icon]" class="w-4 h-4" />
            <span>{{ item.title }}</span>
            </Link>
          </div>
        </div>
      </transition>

      <div class="p-6 rounded-lg bg-gray-100 overflow-auto">
        <h1 class="text-2xl font-semibold text-purple-800">{{ title }}</h1>
        <div class="lg:p-6 rounded-lg bg-gray-100 overflow-auto pb-20 md:pb-6">
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
  HomeIcon,
  ChartBarIcon,
  ChartPieIcon,
  UserIcon,
  BriefcaseIcon,
  CalculatorIcon,
  StopIcon,
  DocumentIcon,
  QuestionMarkCircleIcon,
  AcademicCapIcon,
  EllipsisHorizontalIcon,
  ShieldCheckIcon,
} from '@heroicons/vue/24/outline';

const iconMap = {
  HomeIcon,
  ChartPieIcon,
  ChartBarIcon,
  UserIcon,
  BriefcaseIcon,
  CalculatorIcon,
  StopIcon,
  DocumentIcon,
  QuestionMarkCircleIcon,
  AcademicCapIcon,
  EllipsisHorizontalIcon,
  ShieldCheckIcon,
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
    title: 'Budget Planner',
    icon: 'CalculatorIcon',
    active: currentRoute.startsWith('/user/budget'),
    link: 'budget.index',
  },
  {
    title: 'Debt Manager',
    icon: 'ChartPieIcon',
    active: currentRoute.startsWith('/user/debt'),
    link: 'debt.index'
  },
  {
    title: 'Goal Setting',
    icon: 'StopIcon',
    active: currentRoute.startsWith('/user/goal'),
    link: 'goal.index'
  },
  {
    title: 'Investment Planner',
    icon: 'BriefcaseIcon',
    active: currentRoute.startsWith('/user/invest'),
    link: 'invest.index'
  },
  {
    title: 'Networth Calculator',
    icon: 'ChartBarIcon',
    active: currentRoute.startsWith('/user/networth'),
    link: 'networth.index'
  },
  {
    title: 'Legacy Planning',
    icon: 'ShieldCheckIcon',
    active: currentRoute.startsWith('/user/legacy'),
    link: 'legacy.landing'
  },
  {
    title: 'Calculators',
    icon: 'CalculatorIcon',
    active: currentRoute.startsWith('/user/calculators'),
    link: 'calculator.index'
  },
  {
    title: 'Zuri Score',
    icon: 'DocumentIcon',
    active: currentRoute.startsWith('/user/zuriscore'),
    link: 'zuriscore.index'
  },
  {
    title: 'Questionnaires',
    icon: 'QuestionMarkCircleIcon',
    active: currentRoute.startsWith('/user/questionnaires'),
    link: 'questionnaires.index'
  },
  {
    title: 'Coach',
    icon: 'UserIcon',
    active: currentRoute.startsWith('/user/coach'),
    link: 'coach.index'
  },
]

// Sidebar state - set to true by default
const sidebarOpen = ref(true);

// Toggle sidebar function
const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value;
};

const mobileMenuOpen = ref(false)

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
}

// Visible and hidden menu partitions
const visibleMenuItems = menuItems.slice(0, 7)
const hiddenMenuItems = menuItems.slice(7)

const toggleMoreMenu = () => {
  moreMenuOpen.value = !moreMenuOpen.value
}

const closeMoreMenu = () => {
  moreMenuOpen.value = false
}
// Extended menu: Home, Profile, other leftover items
const extendedMenuItems = [
  {
    title: 'Home',
    icon: 'HomeIcon',
    link: 'home',
    active: currentRoute === '/',
  },
  {
    title: 'Profile',
    icon: 'UserIcon',
    link: 'profile.edit',
    active: currentRoute.startsWith('/profile'),
  },
  ...menuItems.slice(5) // remaining original items
]
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
