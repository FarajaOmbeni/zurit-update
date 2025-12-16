<template>

    <Head title="Budget Planner" />
    <Navbar />
    <div class="mx-6 md:mx-12">
        <BackButton class="mt-6" />
    </div>
    <div class="mt-[7rem]">

        <ToolCardBig title="What is a Budget Planner??
    " description="A Budget Planner is a tool that assists individuals and businesses in outlining their income and expenses, offering a structured plan for managing finances."
            image-url="/images/tools/budget1.webp" />

        <WhyUseTool title="Why Use Our Budget Planner?"
            description="Our Budget Planner isn't just a tool; it's a personalized financial guide designed to:"
            :bullet-points="['Offer customized financial strategies',
                'Provide comprehensive analysis of your income and spending',
                'Guide financial decisions towards your objectives',
                'Help manage and track expenses efficiently']" image-url="/images/tools/budget2.webp" />

        <h2 class="text-4xl font-bold text-center mt-24 mb-6">How it Works</h2>
        <div class="flex flex-col lg:flex-row gap-6 justify-center items-center mb-16">
            <HowItWorksCard title="Step 1: Sign Up"
                description="Get started by signing up for goal setting. It's quick and easy!"
                image-url="/images/tools/goal_step1.webp" />
            <HowItWorksCard title="Step 2: Set Your Budget"
                description="Define your budget. You will be able to keep track of your spendings."
                image-url="/images/tools/goal_step2.webp" />
            <HowItWorksCard title="Step 3: Track and Analyze"
                description="Use our intuitive dashboard to track your goals and get insights."
                image-url="/images/tools/goal_step3.webp" />
        </div>

        <div class="flex justify-center mb-12">
            <ToolFoot title="Budget Planning Made Simple"
                description="At Zurit, we believe in simplifying budgeting. Our tool is designed for easy navigation and user-friendly interfaces."
                buttonText="Go to Budget Planning" buttonLink="/dashboard" />
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
import BackButton from '@/Components/Shared/BackButton.vue';
import { Head } from '@inertiajs/vue3';
</script>