<script setup>
import { ref, computed, onMounted } from "vue";

const testimonials = ref([
    {
        id: 1,
        name: "Ombeni Faraja",
        image: "/images/testimonials/person1.webp",
        review:
            "A review is an evaluation of a publication, product, service, or company or a critical take on current affairs in literature, politics, or culture...",
    },
    {
        id: 2,
        name: "Jane Doe",
        image: "/images/testimonials/person2.webp",
        review:
            "I had a fantastic experience! The service was outstanding, and I highly recommend it to anyone looking for quality and reliability.",
    },
    {
        id: 3,
        name: "John Smith",
        image: "/images/testimonials/person3.webp",
        review:
            "Professional and efficient. This service exceeded my expectations, and I will definitely use it again in the future.",
    },
]);

const currentIndex = ref(0);

const nextTestimonial = () => {
    currentIndex.value = (currentIndex.value + 1) % testimonials.value.length;
};

onMounted(() => {
    setInterval(nextTestimonial, 5000); // Auto-slide every 5 seconds
});

const currentTestimonial = computed(() => testimonials.value[currentIndex.value]);
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
                        <h3 class="text-xl font-semibold text-gray-900">1. Financial Expertise</h3>
                        <p class="text-gray-600">
                            Benefit from our wealth of experience and expertise in wealth management and financial
                            planning.
                            Our track record speaks for itself.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">2. Tailored Solutions</h3>
                        <p class="text-gray-600">
                            We customize financial strategies to meet your unique goals and challenges, ensuring your
                            plan is as individual as you are.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">3. Unwavering Commitment</h3>
                        <p class="text-gray-600">
                            We’re passionately committed to your financial prosperity. Our core values drive us to work
                            tirelessly to help you achieve your financial goals.
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
                    <img :src="currentTestimonial.image" :alt="currentTestimonial.name"
                        class="object-cover w-full h-full transition-all duration-500" />
                </div>

                <h3 class="mt-4 text-xl font-semibold text-gray-900">{{ currentTestimonial.name }}</h3>

                <p class="mt-4 text-gray-600 text-sm md:text-base w-4/5 md:w-3/5 mx-auto">
                    {{ currentTestimonial.review }}
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
