<script setup>
import ListItem from './Shared/ListItem.vue';
</script>
<template>
    <div class="relative w-full h-screen flex items-center justify-center mb-12">

        <!-- Background Image -->
        <img src="/images/home/hero_bg.webp" alt="Background"
            class="absolute inset-0 w-full h-full object-cover rounded-br-[10rem]" />

        <!-- Gradient Overlay -->
        <div
            class="absolute inset-0 bg-gradient-to-r from-[#35389b] via-[#5f55c7] to-[#a882ef] opacity-80 rounded-br-[10rem] overflow-hidden">
        </div>

        <!-- Content -->
        <div
            class="w-full max-w-[30rem] md:max-w-full relative z-10 text-white px-4 md:px-6 flex flex-col justify-center items-center mt-20 md:mt-20 xl:mt-0 text-center font-bold xl:text-left xl:flex-row md:mt-0 gap-6 md:gap-12">
            <!-- Text Content -->
            <div class="flex flex-col max-w-2xl">
                <div class="">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl xl:text-5xl font-extrabold">
                        Welcome to <span class="text-gold-400">Zurit Consulting</span>
                    </h1>

                    <!-- Description -->
                    <p class="my-4 text-sm sm:text-base md:text-md leading-relaxed">
                        Empowering individuals, teams, and enterprises with tailored financial training and advisory
                        solutions:
                    </p>

                    <div class="font-semibold my-4">
                        <ListItem name="Individuals" details="Gaining control over personal finances" />
                        <ListItem name="Trustees" details="Managing pension and investment decisions" />
                        <ListItem name="SACCO Directors" details="Enhancing financial literacy at the group level" />
                        <ListItem name="HR Professionals" details="Creating financially empowered teams" />
                        <ListItem name="MSMEs & Corporates" details="Driving growth through financial planning" />
                    </div>

                    <p>
                        Whether planning your personal finances, managing organizational funds, or leading teams to
                        success, <span class="text-gold-400">Zurit equips you with the tools and insights to
                            thrive.</span>
                    </p>

                    <p class="font-bold text-gold-400 mt-4 text-lg sm:text-xl md:text-xl italic">Let’s make money
                        simple. Let’s make life better.</p>
                </div>
            </div>

            <!-- Video Section -->
            <div class="relative flex justify-center">
                <iframe
                    class="hidden md:block w-[300px] h-[170px] sm:w-[350px] sm:h-[200px] md:w-[450px] md:h-[250px] xl:w-[650px] xl:h-[350px] max-w-full max-h-full rounded-lg shadow-lg"
                    src="https://www.youtube.com/embed/x_g-M07DjNg" frameborder="0" allowfullscreen>
                </iframe>
            </div>
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
