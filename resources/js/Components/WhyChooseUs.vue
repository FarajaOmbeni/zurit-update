<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
    testimonials: { type: Array, default: () => [] },
})

const currentIndex = ref(0)
const nextTestimonial = () => {
    currentIndex.value = (currentIndex.value + 1) % props.testimonials.length
}

onMounted(() => {
    setInterval(nextTestimonial, 5000)
})

const currentTestimonial = computed(
    () => props.testimonials[currentIndex.value] ?? {}
)
</script>

<template>
    <div class="bg-gray-100 py-16 px-6 md:px-12">
        <div class="max-w-6xl mx-auto flex flex-col xl:flex-row text-center xl:text-left gap-12">
            <!-- Why Choose Us -->
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Why Choose Us</h2>
                <p class="mt-4 text-gray-600">
                    At Zurit Financial Consultancy, we stand out in the financial consulting industry for several
                    compelling reasons:
                </p>

                <div class="mt-6 space-y-6">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">1. Experienced Financial Experts</h3>
                        <p class="text-gray-600">
                            Backed by over 20 years of leadership in investments, training, and advisory, our team
                            brings deep expertise and insight to every client engagement.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">2. Customized Financial Strategies</h3>
                        <p class="text-gray-600">
                            We don't do one-size-fits-all. Our approach is tailored to your unique needs—whether you're
                            a business leader, employee, trustee, or entrepreneur.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">3. Unwavering Commitment to Your Prosperity</h3>
                        <p class="text-gray-600">
                            Your growth is our mission. From tools to training, we walk with you step-by-step toward a financially empowered future.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Vertical Divider -->
            <div class="hidden md:block w-[1px] bg-gray-300"></div>

            <!-- Testimonials -->
            <div class="flex flex-col items-center text-center relative w-full">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Testimonials</h2>

                <div
                    class="mt-6 w-40 h-40 md:w-48 md:h-48 rounded-full overflow-hidden shadow-lg border-4 border-yellow-400">
                    <img :src="`storage/testimonials/${currentTestimonial.image}`" :alt="currentTestimonial.name"
                        class="object-cover w-full h-full transition-all duration-500" />
                </div>

                <h3 class="mt-4 text-xl font-semibold text-gray-900">{{ currentTestimonial.name }}</h3>

                <p class="mt-4 text-gray-600 text-sm md:text-base w-4/5 md:w-3/5 mx-auto">
                    {{ currentTestimonial.content }}
                </p>

                <!-- Navigation Dots -->
                <div class="flex mt-4 space-x-2">
                    <span v-for="(testimonial, index) in testimonials" :key="testimonial.id"
                        @click="currentIndex = index" class="w-3 h-3 rounded-full cursor-pointer"
                        :class="index === currentIndex ? 'bg-yellow-500' : 'bg-gray-300'"></span>
                </div>
            </div>

        </div>
    </div>
</template>
