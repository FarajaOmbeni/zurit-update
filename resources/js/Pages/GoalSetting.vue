<template>
    <Head title="Goal Setting" />
    <Navbar />
    <div class="mt-[7rem]">

        <ToolCardBig title="What is Goal Setting?
    " description="Goal setting is thinking about your ideal future and for motivating yourself to turn your vision of this future into reality. The process of setting goals helps you choose where you want to go in life. By knowing precisely what you want to achieve, you know where you have to concentrate your efforts. You'll also quickly spot the distractions that can, so easily, lead you astray."
            image-url="/images/tools/goal.png" />

        <WhyUseTool title="Why Use our Goal Setting Tool"
            description="Our Goal Setting tool isn\'t just a tool: it\'s a personalized financial guide designed to:"
            :bullet-points="['Help you manage your goals',
                'Help you contribute to your goals',
                'Have a whole view of all your goals',
                'See the progress of each goal']" image-url="/images/tools/budget2.webp" />

        <h2 class="text-4xl font-bold text-center mt-24 mb-6">How it Works</h2>
        <div class="flex flex-col lg:flex-row gap-6 justify-center items-center mb-16">
            <HowItWorksCard title="Step 1: Sign Up"
                description="Get started by signing up for goal setting. It's quick and easy!"
                image-url="/images/tools/goal_step1.webp" />
            <HowItWorksCard title="Step 2: Set Your Goal"
                description="Define your financial goals and priorities. Whether it's buying a car, investing, education, or entertainment, we've got you covered."
                image-url="/images/tools/goal_step2.webp" />
            <HowItWorksCard title="Step 3: Track and Analyze"
                description="Use our intuitive dashboard to track your goals and get insights."
                image-url="/images/tools/goal_step3.webp" />
        </div>

        <div class="flex justify-center mb-12">
            <ToolFoot title="Goal Setting Made Simple" description="At Zurit, we believe in goal setting. Our tool is designed for easy navigation and user-friendly interfaces." buttonText="Go to Goal Setting" buttonLink="#"/>
        </div>

    </div>
    <Footer />
</template>

<script setup>
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import ToolCardBig from '@/Components/Shared/ToolCardBig.vue';
import WhyUseTool from '@/Components/Shared/WhyUseTool.vue';
import HowItWorksCard from '@/Components/Shared/HowItWorksCard.vue';
import ToolFoot from '@/Components/Shared/ToolFoot.vue';
import { Head } from '@inertiajs/vue3';

</script>