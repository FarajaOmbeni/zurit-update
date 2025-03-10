<script setup>
import GoldButton from '../Components/Shared/GoldButton.vue';
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
            class="w-[30rem] md:w-full relative z-10 text-white px-6 flex flex-col justify-center items-center mt-20 md:mt-20 xl:mt-0 text-center font-bold xl:text-left xl:flex-row md:mt-0 gap-12">
            <!-- Text Content -->
            <div class="flex flex-col max-w-2xl">
                <div class="">
                    <h1 class="text-3xl md:text-4xl xl:text-5xl font-extrabold">
                        Welcome to <span class="text-gold-400">Zurit Consulting</span>
                    </h1>

                    <!-- Description -->
                    <p class="mt-4 md:text-lg leading-relaxed">
                        Unlocking financial prosperity with Zurit Consulting. Our tailored financial trainings and
                        advisory solutions empower <span class="text-gold-300 font-semibold">trustees, corporates, and
                            individuals</span> to navigate the complexities of fincance with confidence.
                    </p>
                    <p class="font-bold text-gold-400 mt-4 text-xl md:text-2xl">Below are the trainings we offer: </p>
                </div>

                <!-- Buttons Section -->
                <div class="mt-6 grid grid-cols-2 gap-4 w-full max-w-md text-center mx-auto xl:mx-0">
                    <GoldButton url="training" button_name="Individual Training" />
                    <GoldButton button_name="Corporate Training" url="training" />
                    <GoldButton button_name="Wealth Management" url="training" />
                    <GoldButton button_name="Financial Advisory" url="training" />
                </div>

            </div>

            <!-- Video Section -->
            <div class="relative flex justify-center">
                <iframe
                    class="w-[350px] h-[200px] sm:w-[450px] sm:h-[250px] xl:w-[650px] xl:h-[350px] max-w-full max-h-full rounded-lg shadow-lg"
                    src="https://www.youtube.com/embed/YOUR_VIDEO_ID" frameborder="0" allowfullscreen>
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
