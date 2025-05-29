<template>
    <div class="my-12 flex flex-col items-center">
        <div>
            <p class="text-center text-4xl md:text-5xl font-extrabold text-yellow-500 mb-10">Contact Us</p>
        </div>
        <div
            class="bg-gradient-to-r rounded-lg shadow-xl from-purple-700 to-indigo-900 py-12 px-6 md:px-12 md:mx-[10%] xl:mx-[20%]">
            <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="flex gap-4 flex-col items-center text-center text-white">
                    <div>
                        <p class="mb-4 text-lg font-bold">Follow Us</p>
                        <div class="flex space-x-4">
                            <a href="https://www.facebook.com/ZuritConsultingKE" target="_blank"
                                class="hover:opacity-75">
                                <img src="/images/icons/facebook.svg" alt="Facebook" class="w-8 h-8">
                            </a>
                            <a href="https://www.twitter.com/ZuritConsulting" target="_blank" class="hover:opacity-75">
                                <img src="/images/icons/twitter.svg" alt="Twitter" class="w-8 h-8">
                            </a>
                            <a href="https://www.linkedin.com/company/zuritconsultingke" target="_blank"
                                class="hover:opacity-75">
                                <img src="/images/icons/linkedin.svg" alt="LinkedIn" class="w-8 h-8">
                            </a>
                            <a href="https://www.instagram.com/zuritconsultingke/" target="_blank"
                                class="hover:opacity-75">
                                <img src="/images/icons/instagram.svg" alt="Instagram" class="w-8 h-8">
                            </a>
                            <a href="https://www.tiktok.com/@zurit_consulting" target="_blank" class="hover:opacity-75">
                                <img src="/images/icons/tiktok.svg" alt="Tiktok" class="w-8 h-8">
                            </a>
                        </div>
                    </div>

                    <!-- Google Map -->
                    <div class="relative w-full h-96">
                        <iframe class="w-full h-full rounded-lg shadow-lg"
                            src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15955.462377226684!2d36.802144!3d-1.310786!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f1a919fef3ad7%3A0xa95e5722bff527e4!2sMbagathi%20Hospital%20Rd%2C%20Nairobi%2C%20Kenya!5e0!3m2!1sen!2ske!4v1708439485865!5m2!1sen!2ske"
                            allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                        </iframe>

                        <!-- Clickable Overlay to Open Google Maps -->
                        <a href="https://www.google.com/maps?q=-1.310786,36.802144" target="_blank"
                            class="absolute inset-0 bg-transparent cursor-pointer"></a>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="flex flex-col items-center text-center text-white">
                    <form @submit.prevent="handleSubmit">
                        <div class="w-full max-w-md">
                            <!-- Honeypot Field -->
                            <input type="text" name="website" v-model="form.website" style="display:none" tabindex="-1"
                                autocomplete="off" />

                            <input required type="text" placeholder="Your Name" v-model="form.name"
                                class="w-full px-4 py-3 mb-4 rounded-full shadow-lg text-gray-900" />
                            <input required type="email" placeholder="Your Email" v-model="form.email"
                                class="w-full px-4 py-3 mb-4 rounded-full shadow-lg text-gray-900" />
                            <textarea required placeholder="Your Message" v-model="form.message"
                                class="w-full px-4 py-3 mb-4 rounded-lg shadow-lg text-gray-900"></textarea>

                            <button type="submit"
                                class="w-full bg-yellow-500 text-purple-900 font-semibold py-2 rounded-md hover:bg-yellow-400 transition"
                                :class="form.processing ? 'opacity-50 cursor-not-allowed' : 'block'"
                                :disabled="form.processing">
                                {{ form.processing ? 'Sending...' : 'Send Message' }}
                            </button>
                            <!-- Success Message -->
                            <p v-if="showSuccess" class="text-green-300">
                                Message sent successfully! We will get back to you soon!
                            </p>
                        </div>
                    </form>

                    <!-- Contact Details -->
                    <div class="mt-6 text-sm md:text-base">
                        <p class="mb-2">+254 759 092 412</p>
                        <p class="mb-2">Zuider Complex, Mbagathi Hospital Road, Off Mbagathi Way</p>
                        <p class="mb-2">info@zuritconsulting.com</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
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
        sessionStorage.removeItem('scrollPosition'); 
    }
}

const showSuccess = ref(false);

const form = useForm({
    name: '',
    email: '',
    message: '',
    website: ''
})

const handleSubmit = () => {
    saveScrollPosition();  

    form.post(route('send.message'), {
        onSuccess: () => {
            form.reset()
            showSuccess.value = true;
            nextTick(() => {
                setTimeout(() => {
                    showSuccess.value = false;
                }, 5000);
            });

            restoreScrollPosition();  // Restore scroll position after submission
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors)
                .flat()
                .join(' ');

            openAlert('danger', errorMessages, 5000);
        }
    })
}

// Restore scroll position on page load
restoreScrollPosition();
</script>