<template>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-inner">
                <!-- Logo -->
                <div class="nav-left">
                    <Link href="/" class="logo-link">
                        <div class="logo-mark">
                            <span class="logo-text">Zurit</span>
                            <div class="logo-icon"></div>
                        </div>
                    </Link>
                </div>

                <!-- Desktop Navigation -->
                <nav class="nav-center">
                    <Link
                        href="/"
                        :class="[
                            'nav-link',
                            { 'nav-link-active': isCurrentRoute('/') },
                        ]"
                    >
                        Home
                    </Link>
                    <Link
                        href="/about"
                        :class="[
                            'nav-link',
                            { 'nav-link-active': isCurrentRoute('/about') },
                        ]"
                    >
                        About Us
                    </Link>

                    <!-- Solutions Dropdown -->
                    <div class="nav-dropdown">
                        <button
                            @click="toggleDropdown('solutions')"
                            class="nav-link dropdown-trigger"
                        >
                            Solutions
                            <svg
                                class="dropdown-icon"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                        <div
                            v-if="activeDropdown === 'solutions'"
                            class="dropdown-menu"
                            @click.outside="closeDropdowns"
                        >
                            <Link
                                v-for="item in solutionsMenu"
                                :key="item.href"
                                :href="item.href"
                                class="dropdown-item"
                            >
                                {{ item.name }}
                            </Link>
                        </div>
                    </div>

                    <Link
                        href="/blogs"
                        :class="[
                            'nav-link',
                            { 'nav-link-active': isCurrentRoute('/blogs') },
                        ]"
                    >
                        Learn
                    </Link>
                    <Link
                        href="/books"
                        :class="[
                            'nav-link',
                            { 'nav-link-active': isCurrentRoute('/books') },
                        ]"
                    >
                        Books
                    </Link>
                </nav>

                <!-- Right Side -->
                <div class="nav-right">
                    <!-- User Menu if logged in -->
                    <div v-if="user" class="nav-dropdown">
                        <button
                            @click="toggleDropdown('user')"
                            class="btn-user"
                        >
                            {{ user.name }}
                            <svg
                                class="dropdown-icon"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                        <div
                            v-if="activeDropdown === 'user'"
                            class="dropdown-menu dropdown-menu-right"
                            @click.outside="closeDropdowns"
                        >
                            <!-- Admin Dashboard -->
                            <template v-if="user.role === 1">
                                <Link href="/admin" class="dropdown-item">
                                    Admin Dashboard
                                </Link>
                            </template>
                            <!-- Coach Dashboard -->
                            <template v-if="user.role === 2">
                                <Link href="/coach" class="dropdown-item">
                                    Coach Dashboard
                                </Link>
                            </template>
                            <Link href="/msme/dashboard" class="dropdown-item"
                                >MSME Dashboard</Link
                            >
                            <Link href="/user/budget" class="dropdown-item"
                                >Prosperity Dashboard</Link
                            >
                            <Link
                                href="/elearning/landing"
                                class="dropdown-item"
                                >E-Learning</Link
                            >
                            <Link href="/profile" class="dropdown-item"
                                >Profile</Link
                            >
                            <Link
                                href="/logout"
                                method="post"
                                class="dropdown-item"
                                >Log Out</Link
                            >
                        </div>
                    </div>

                    <!-- Prosperity Dashboard Button / Login -->
                    <Link
                        v-if="user"
                        href="/msme/dashboard"
                        class="btn btn-primary"
                    >
                        Prosperity Dashboard
                    </Link>
                    <Link v-else href="/login" class="btn btn-primary">
                        Prosperity Dashboard
                    </Link>
                </div>

                <!-- Mobile Menu Button -->
                <button
                    @click="isMobileMenuOpen = !isMobileMenuOpen"
                    class="mobile-menu-button"
                >
                    <svg
                        class="menu-icon"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            v-if="!isMobileMenuOpen"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                        <path
                            v-else
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Slide-in Panel -->
        <transition name="slide">
            <div v-if="isMobileMenuOpen" class="mobile-menu">
                <div class="mobile-menu-header">
                    <Link href="/" @click="isMobileMenuOpen = false">
                        <div class="logo-mark">
                            <span class="logo-text">Zurit</span>
                            <div class="logo-icon"></div>
                        </div>
                    </Link>
                    <button
                        @click="isMobileMenuOpen = false"
                        class="mobile-close-button"
                    >
                        <svg
                            class="menu-icon"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <div class="mobile-menu-content">
                    <Link
                        href="/"
                        class="mobile-link"
                        @click="isMobileMenuOpen = false"
                        >Home</Link
                    >
                    <Link
                        href="/about"
                        class="mobile-link"
                        @click="isMobileMenuOpen = false"
                        >About Us</Link
                    >

                    <!-- Solutions Accordion -->
                    <div class="mobile-accordion">
                        <button
                            @click="toggleMobileAccordion('solutions')"
                            class="mobile-accordion-trigger"
                        >
                            Solutions
                            <svg
                                :class="[
                                    'dropdown-icon',
                                    {
                                        'rotate-180':
                                            activeMobileAccordion ===
                                            'solutions',
                                    },
                                ]"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                        <div
                            v-if="activeMobileAccordion === 'solutions'"
                            class="mobile-accordion-content"
                        >
                            <Link
                                v-for="item in solutionsMenu"
                                :key="item.href"
                                :href="item.href"
                                class="mobile-accordion-link"
                                @click="isMobileMenuOpen = false"
                            >
                                {{ item.name }}
                            </Link>
                        </div>
                    </div>

                    <Link
                        href="/blogs"
                        class="mobile-link"
                        @click="isMobileMenuOpen = false"
                        >Learn</Link
                    >
                    <Link
                        href="/books"
                        class="mobile-link"
                        @click="isMobileMenuOpen = false"
                        >Books</Link
                    >

                    <!-- User Menu for Mobile -->
                    <div v-if="user" class="mobile-accordion">
                        <button
                            @click="toggleMobileAccordion('user')"
                            class="mobile-accordion-trigger mobile-user-button"
                        >
                            {{ user.name }}
                            <svg
                                :class="[
                                    'dropdown-icon',
                                    {
                                        'rotate-180':
                                            activeMobileAccordion === 'user',
                                    },
                                ]"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                        <div
                            v-if="activeMobileAccordion === 'user'"
                            class="mobile-accordion-content"
                        >
                            <template v-if="user.role === 1">
                                <Link
                                    href="/admin"
                                    class="mobile-accordion-link"
                                    @click="isMobileMenuOpen = false"
                                >
                                    Admin Dashboard
                                </Link>
                            </template>
                            <template v-if="user.role === 2">
                                <Link
                                    href="/coach"
                                    class="mobile-accordion-link"
                                    @click="isMobileMenuOpen = false"
                                >
                                    Coach Dashboard
                                </Link>
                            </template>
                            <Link
                                href="/msme/dashboard"
                                class="mobile-accordion-link"
                                @click="isMobileMenuOpen = false"
                                >MSME Dashboard</Link
                            >
                            <Link
                                href="/user/budget"
                                class="mobile-accordion-link"
                                @click="isMobileMenuOpen = false"
                                >Prosperity Dashboard</Link
                            >
                            <Link
                                href="/elearning/landing"
                                class="mobile-accordion-link"
                                @click="isMobileMenuOpen = false"
                                >E-Learning</Link
                            >
                            <Link
                                href="/profile"
                                class="mobile-accordion-link"
                                @click="isMobileMenuOpen = false"
                                >Profile</Link
                            >
                            <Link
                                href="/logout"
                                method="post"
                                class="mobile-accordion-link"
                                @click="isMobileMenuOpen = false"
                                >Log Out</Link
                            >
                        </div>
                    </div>
                    <Link
                        v-else
                        href="/login"
                        class="btn btn-primary mobile-login-button"
                        @click="isMobileMenuOpen = false"
                    >
                        Log in
                    </Link>
                </div>
            </div>
        </transition>
    </nav>
</template>

<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);

const isMobileMenuOpen = ref(false);
const activeDropdown = ref(null);
const activeMobileAccordion = ref(null);

const solutionsMenu = [
    { name: "MSME Dashboard", href: "/msme/dashboard" },
    { name: "Goal Setting", href: "/goal-setting" },
    { name: "Budget Planner", href: "/budget-planner" },
    { name: "Networth Calculator", href: "/networth-calculator" },
    { name: "Debt Manager", href: "/debt-manager" },
    { name: "Investment Planner", href: "/investment-planner" },
    { name: "Zuriscore", href: "/zuriscore" },
    { name: "Calculators", href: "/calculators" },
    { name: "Questionnaires", href: "/questionnaires" },
    { name: "Training", href: "/training" },
    { name: "Advisory", href: "/advisory" },
    { name: "Business Support", href: "/business-support" },
];

const toggleDropdown = (name) => {
    activeDropdown.value = activeDropdown.value === name ? null : name;
};

const closeDropdowns = () => {
    activeDropdown.value = null;
};

const toggleMobileAccordion = (name) => {
    activeMobileAccordion.value =
        activeMobileAccordion.value === name ? null : name;
};

const isCurrentRoute = (path) => {
    return window.location.pathname === path;
};
</script>

<style scoped>
/* NAVBAR */
.navbar {
    position: fixed;
    width: 100%;
    top: 0;
    z-index: 50;
    background: #ffffff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.nav-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

.nav-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 80px;
}

/* Logo */
.logo-link {
    text-decoration: none;
}

.logo-mark {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.logo-text {
    font-weight: 700;
    font-size: 1.5rem;
    color: #000000;
}

.logo-icon {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    background: linear-gradient(135deg, #6b27ff, #ff5c5c);
}

/* Desktop Navigation */
.nav-center {
    display: none;
    gap: 2.5rem;
    font-size: 1rem;
}

@media (min-width: 1024px) {
    .nav-center {
        display: flex;
    }
}

.nav-link {
    position: relative;
    padding: 0.5rem 0;
    color: #333333;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
    cursor: pointer;
    background: none;
    border: none;
    font-family: inherit;
    font-size: inherit;
}

.nav-link:hover {
    color: #6b27ff;
}

.nav-link-active {
    color: #000000;
}

.nav-link-active::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 2px;
    border-radius: 999px;
    background: #ff5c5c;
}

/* Dropdown */
.nav-dropdown {
    position: relative;
}

.dropdown-trigger {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.dropdown-icon {
    width: 16px;
    height: 16px;
    transition: transform 0.3s ease;
}

.rotate-180 {
    transform: rotate(180deg);
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    margin-top: 0.5rem;
    min-width: 220px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 0.5rem 0;
    z-index: 100;
}

.dropdown-menu-right {
    left: auto;
    right: 0;
}

.dropdown-item {
    display: block;
    padding: 0.75rem 1.25rem;
    color: #333333;
    text-decoration: none;
    transition: background 0.2s ease;
}

.dropdown-item:hover {
    background: #f5f5f5;
}

/* Buttons */
.btn {
    border: none;
    cursor: pointer;
    border-radius: 999px;
    padding: 0.7rem 1.6rem;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #6b27ff, #c02dfc);
    color: #ffffff;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(107, 39, 255, 0.4);
}

.btn-user {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.2rem;
    border-radius: 999px;
    background: #f5f5f5;
    border: none;
    cursor: pointer;
    font-weight: 500;
    font-size: 0.95rem;
    transition: background 0.3s ease;
}

.btn-user:hover {
    background: #e5e5e5;
}

.nav-right {
    display: none;
    gap: 1rem;
    align-items: center;
}

@media (min-width: 1024px) {
    .nav-right {
        display: flex;
    }
}

/* Mobile Menu Button */
.mobile-menu-button {
    display: flex;
    align-items: center;
    justify-content: center;
    background: none;
    border: none;
    cursor: pointer;
    color: #333333;
}

@media (min-width: 1024px) {
    .mobile-menu-button {
        display: none;
    }
}

.menu-icon {
    width: 24px;
    height: 24px;
}

/* Mobile Menu */
.mobile-menu {
    position: fixed;
    top: 0;
    bottom: 0;
    right: 0;
    width: 320px;
    max-width: 85vw;
    background: #ffffff;
    box-shadow: -5px 0 25px rgba(0, 0, 0, 0.15);
    z-index: 100;
    overflow-y: auto;
}

.mobile-menu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e5e5;
}

.mobile-close-button {
    background: none;
    border: none;
    cursor: pointer;
    color: #333333;
}

.mobile-menu-content {
    padding: 1.5rem 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.mobile-link {
    padding: 1rem 1rem;
    color: #333333;
    text-decoration: none;
    border-radius: 8px;
    transition: background 0.2s ease;
    font-weight: 500;
}

.mobile-link:hover {
    background: #f5f5f5;
}

.mobile-accordion {
    border-radius: 8px;
}

.mobile-accordion-trigger {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1rem;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 500;
    color: #333333;
    border-radius: 8px;
    transition: background 0.2s ease;
}

.mobile-accordion-trigger:hover {
    background: #f5f5f5;
}

.mobile-user-button {
    background: #f5f5f5;
    margin-top: 1rem;
}

.mobile-accordion-content {
    padding: 0.5rem 0 0.5rem 1rem;
    display: flex;
    flex-direction: column;
}

.mobile-accordion-link {
    padding: 0.75rem 1rem;
    color: #666666;
    text-decoration: none;
    border-radius: 8px;
    transition: background 0.2s ease;
}

.mobile-accordion-link:hover {
    background: #f5f5f5;
}

.mobile-login-button {
    margin-top: 1rem;
    width: 100%;
    text-align: center;
}

/* Slide animation */
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
