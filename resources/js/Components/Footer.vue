<template>
    <footer class="bg-purple-900 text-center md:text-left text-gray-300 py-10">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Address Section -->
                <div class="space-y-3">
                    <h2 class="text-lg font-semibold text-yellow-500">Contact Us</h2>
                    <p>Zuidier Complex</p>
                    <p>Ngumo, Off Mbagathi Road</p>
                    <p>Nairobi, KE</p>
                    <p>Phone: <a href="tel:+254759092412" class="hover:text-yellow-400">+254 759 092 412</a></p>
                    <p>Email: <a href="mailto:info@zuritconsulting.com"
                            class="hover:text-yellow-400">info@zuritconsulting.com</a></p>
                </div>

                <!-- Prosperity Tools -->
                <div class="space-y-3">
                    <h2 class="text-lg font-semibold text-yellow-500">Prosperity Tools</h2>
                    <Link :href="route('budget')" class="block hover:text-yellow-400">Budget Planner</Link>
                    <Link :href="route('networth')" class="block hover:text-yellow-400">Networth
                    Calculator</Link>
                    <Link :href="route('debt')" class="block hover:text-yellow-400">Debt Manager</Link>
                    <Link :href="route('investment')" class="block hover:text-yellow-400">Investment Planner
                    </Link>
                </div>

                <!-- Quick Links -->
                <div class="space-y-3">
                    <h2 class="text-lg font-semibold text-yellow-500">Quick Links</h2>
                    <a href="{{ url('/') }}" class="block hover:text-yellow-400">Home</a>
                    <Link :href="route('training')" class="block hover:text-yellow-400">Training</Link>
                    <a href="{{ url('about') }}" class="block hover:text-yellow-400">About Us</a>
                    <a href="/#contact-us" class="block hover:text-yellow-400">Contact Us</a>
                </div>

                <!-- Newsletter -->
                <div class="space-y-3">
                    <h2 class="text-lg font-semibold text-yellow-500">Get In Touch</h2>
                    <form action="{{ route('subscribe') }}" method="post" class="space-y-3">
                        <input type="email" name="email" placeholder="Your Email"
                            class="w-full p-2 rounded-md bg-gray-800 text-white placeholder-gray-400 focus:ring-2 focus:ring-yellow-500 focus:outline-none"
                            required>
                        <button type="submit"
                            class="w-full bg-yellow-500 text-purple-900 font-semibold py-2 rounded-md hover:bg-yellow-400 transition">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>

            <!-- Copyright -->
            <div class="text-center text-gray-400 mt-10">
                <p>&copy; 2025 ZURIT CONSULTING. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

</template>
 
<script setup>
import { Link } from '@inertiajs/vue3';

</script>