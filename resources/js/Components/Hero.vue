<script setup>
import ListItem from './Shared/ListItem.vue';
import { ref, onMounted, onUnmounted } from 'vue';

const currentSlide = ref(0);
let slideInterval = null;

const slides = [
    {
        name: "Individuals",
        details: "Gaining control over personal finances.",
        icon: "💰"
    },
    {
        name: "Trustees",
        details: "Managing pension and investment decisions",
        icon: "📊"
    },
    {
        name: "SACCO Directors",
        details: "Enhancing financial literacy at the group level.",
        icon: "🏛️"
    },
    {
        name: "HR Professionals",
        details: "Creating financially empowered teams.",
        icon: "👥"
    },
    {
        name: "MSMEs & Corporates",
        details: "Driving growth through financial planning.",
        icon: "📈"
    }
];

const nextSlide = () => {
    currentSlide.value = (currentSlide.value + 1) % slides.length;
};

const goToSlide = (index) => {
    currentSlide.value = index;
};

const startAutoSlide = () => {
    slideInterval = setInterval(nextSlide, 4000);
};

const stopAutoSlide = () => {
    if (slideInterval) {
        clearInterval(slideInterval);
        slideInterval = null;
    }
};

onMounted(() => {
    startAutoSlide();
});

onUnmounted(() => {
    stopAutoSlide();
});
</script>
<template>
    <div class="relative w-full h-screen flex items-center justify-center mb-12">

        <!-- Background Image -->
        <img src="/images/home/hero_bg.webp" alt="Background" class="absolute inset-0 w-full h-full object-cover" />

        <!-- Gradient Overlay -->
        <div
            class="absolute inset-0 bg-gradient-to-r from-[#35389b] via-[#5f55c7] to-[#a882ef] opacity-80 overflow-hidden">
        </div>

        <!-- Content -->
        <div class="w-full max-w-6xl relative z-10 text-white px-4 md:px-6 flex flex-col justify-center items-center text-center"
            @mouseenter="stopAutoSlide" @mouseleave="startAutoSlide">

            <!-- Main Title -->
            <h1 class="text-4xl mt-28 sm:text-3xl md:text-4xl xl:text-5xl font-extrabold lg:mb-4">
                Welcome to <span class="text-gold-400">Zurit Consulting</span>
            </h1>

            <div class="flex flex-col items-center justify-center lg:w-full my-4">
                <p class="text-lg md:text-lg lg:text-xl xl:text-2xl leading-relaxed font-medium">
                    Empowering individuals, teams, and enterprises with tailored financial training and advisory
                    solutions:
                </p>
            </div>

            <!-- Slideshow Container -->
            <div class="relative w-full max-w-3xl h-64 md:h-70 overflow-hidden rounded-lg">

                <!-- Slides -->
                <div class="relative w-full h-full">
                    <div v-for="(slide, index) in slides" :key="index" :class="['absolute inset-0 transition-all duration-500 ease-in-out transform',
                        index === currentSlide ? 'translate-x-0 opacity-100' :
                            index < currentSlide ? '-translate-x-full opacity-0' : 'translate-x-full opacity-0']">
                        <div class="flex flex-col justify-center items-center h-full">
                            <div class="text-center">
                                <div class="text-8xl md:text-7xl xl:text-8xl mb-6">
                                    {{ slide.icon }}
                                </div>
                                <h2 class="text-3xl md:text-3xl xl:text-4xl font-bold text-gold-400 mb-6">
                                    {{ slide.name }}
                                </h2>
                                <p class="text-xl md:text-xl xl:text-2xl leading-relaxed font-medium">
                                    {{ slide.details }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <!-- Slide Indicators -->
            <div class="flex space-x-3">
                <button v-for="(slide, index) in slides" :key="index" @click="goToSlide(index)" :class="['w-3 h-3 rounded-full transition-all duration-200',
                    index === currentSlide ? 'bg-gold-400' : 'bg-white bg-opacity-50 hover:bg-opacity-75']">
                </button>
            </div>

            <!-- Tagline -->
            <p class="font-bold text-gold-400 mt-8 text-xl md:text-2xl xl:text-3xl italic">
                Making money simple. Making life better.
            </p>
        </div>
    </div>
</template>

<style>
html,
body {
    overflow-x: hidden;
    width: 100%;
}
</style>