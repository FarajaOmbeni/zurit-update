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
        <div v-for="(item, index) in menuItems" :key="index" class="px-4 py-2">
          <Link :href="route(item.link)"
            class="flex items-center py-2 px-2 rounded hover:bg-purple-700 transition-colors"
            :class="item.active ? 'bg-purple-700' : ''">
          <span class="text-yellow-400">
            <component :is="item.icon" class="h-5 w-5" />
          </span>
          <span v-if="sidebarOpen" class="ml-3 whitespace-nowrap">{{ item.title }}</span>
          </Link>
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
import Alert from '@/Components/Shared/Alert.vue';

defineProps({
  title: String,
})

const dropdownOpen = ref(false);
const page = usePage();
const currentRoute = page.url;

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
});

// Clean up event listener when component is unmounted
onUnmounted(() => {
  document.removeEventListener('click', closeDropdown);
});

// Menu items with icons (using heroicons)
const menuItems = [
  {
    title: 'Budget Planner',
    icon: 'HomeIcon',
    active: currentRoute.startsWith('/user/budget'),
    link: 'budget.index',
  },
  {
    title: 'Debt Manager',
    icon: 'ChartBarIcon',
    active: currentRoute.startsWith('/user/debt'),
    link: 'debt.index'
  },
  {
    title: 'Goal Setting',
    icon: 'DocumentTextIcon',
    active: currentRoute.startsWith('/user/goal'),
    link: 'goal.index'
  },
  {
    title: 'Investment Planner',
    icon: 'CogIcon',
    active: currentRoute.startsWith('/user/invest'),
    link: 'invest.index'
  },
  {
    title: 'Networth Calculator',
    icon: 'UserGroupIcon',
    active: currentRoute.startsWith('/user/networth'),
    link: 'networth.index'
  },
  {
    title: 'Calculators',
    icon: 'UserGroupIcon',
    active: currentRoute.startsWith('/user/calculators'),
    link: 'calculator.index'
  },
  {
    title: 'Zuri Score',
    icon: 'UserGroupIcon',
    active: currentRoute.startsWith('/user/zuriscore'),
    link: 'zuriscore.index'
  }
]

// Sidebar state - set to false by default
const sidebarOpen = ref(false);

// Toggle sidebar function
const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value;
};
</script>