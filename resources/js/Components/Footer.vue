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

                <!-- Prosperity Dashboard -->
                <div class="space-y-3">
                    <h2 class="text-lg font-semibold text-yellow-500">Prosperity Dashboard</h2>
                    <Link :href="route('budget')" class="block hover:text-yellow-400">Budget Planner</Link>
                    <Link :href="route('networth')" class="block hover:text-yellow-400">Networth
                    Calculator</Link>
                    <Link :href="route('debt')" class="block hover:text-yellow-400">Debt Manager</Link>
                    <Link :href="route('investment')" class="block hover:text-yellow-400">Investment Planner
                    </Link>
                    <Link :href="route('goal')" class="block hover:text-yellow-400">Goal Setting</Link>
                    <Link :href="route('money-quiz')" class="block hover:text-yellow-400">Money Quiz</Link>
                </div>

                <!-- Quick Links -->
                <div class="space-y-3">
                    <h2 class="text-lg font-semibold text-yellow-500">Quick Links</h2>
                    <Link :href="route('home')" class="block hover:text-yellow-400">Home</Link>
                    <Link :href="route('training')" class="block hover:text-yellow-400">Training</Link>
                    <Link :href="route('about')" class="block hover:text-yellow-400">About Us</Link>
                </div>

                <!-- Newsletter -->
                <div class="space-y-3">
                    <h2 class="text-lg font-semibold text-yellow-500">Get In Touch</h2>
                    <form @submit.prevent="handleSubmit" class="space-y-3">
                        <!-- Honeypot Field -->
                        <input type="text" name="website" v-model="form.website" style="display:none" tabindex="-1"
                            autocomplete="off" />

                        <input type="email" name="email" v-model="form.email" placeholder="Your Email"
                            class="w-full p-2 rounded-md bg-gray-800 text-white placeholder-gray-400 focus:ring-2 focus:ring-yellow-500 focus:outline-none"
                            required>
                        <!-- Success Message -->
                        <p v-if="showSuccess" class="text-green-300">
                            Email sent successfully! We will get back to you soon!
                        </p>
                        <button type="submit"
                            class="w-full bg-yellow-500 text-purple-900 font-semibold py-2 rounded-md hover:bg-yellow-400 transition"
                            :class="form.processing ? 'opacity-50 cursor-not-allowed' : 'block'"
                            :disabled="form.processing">
                            {{ form.processing ? 'Submitting...' : 'Subscribe' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Copyright -->
            <div class="text-center text-gray-400 mt-10">
                <p>&copy; 2025 ZURIT CONSULTING. All Rights Reserved.</p>
                <p><Link class="text-blue-500 underline" href="/terms-and-conditions" target="_blank">Terms and conditions</Link> apply.</p>
            </div>
        </div>
    </footer>

</template>
 
<script setup>
import { Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3'
import { ref, nextTick } from 'vue'

// Save scroll position before submitting
const saveScrollPosition = () => {
    sessionStorage.setItem('scrollPosition', window.scrollY);
}

// Restore scroll position after the form is submitted or page reloads
const restoreScrollPosition = () => {
    const savedPosition = sessionStorage.getItem('scrollPosition');
    if (savedPosition) {
        window.scrollTo(0, savedPosition);
        sessionStorage.removeItem('scrollPosition'); // Clear the saved position after restoring
    }
}

const showSuccess = ref(false);


const form = useForm({
    email: '',
    website: ''
})

const handleSubmit = () => {
    saveScrollPosition();  

    form.post(route('send.email'), {
        onSuccess: () => {
            form.reset();
            showSuccess.value = true;
            nextTick(() => {
                setTimeout(() => {
                    showSuccess.value = false;
                }, 5000);
            });

            restoreScrollPosition();
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors)
                .flat()
                .join(' ');

            openAlert('danger', errorMessages, 5000);
        }
    })
}
restoreScrollPosition();
</script>