<template>
    <Head title="Networth Calculator"/>
    <Navbar />
    <div class="mt-[7rem]">

        <ToolCardBig title="What is a Networth Calculator?"
            description="A Net Worth Calculator is a financial tool that evaluates an individual's wealth by subtracting their liabilities (debts) from their assets. It offers insights into one's financial health."
            image-url="/images/tools/networth1.webp" />

        <WhyUseTool title="Why Use Our Networth Calculator?"
            description="Our Net Worth Calculator isn't just a tool; it's a personalized financial guide designed to:"
            :bullet-points="['Offer personalized financial insights',
                'Provide comprehensive analysis of your assets and liabilities',
                'Guide financial decisions towards your objectives',
                'Help assess your overall financial health']" image-url="/images/tools/networth2.webp" />

        <h2 class="text-4xl font-bold text-center mt-24 mb-6">How it Works</h2>
        <div class="flex flex-col lg:flex-row gap-6 justify-center items-center mb-16">
            <HowItWorksCard title="Step 1: Sign Up"
                description="Get started by signing up for goal net worth calculator. It's quick and easy!"
                image-url="/images/tools/goal_step1.webp" />
            <HowItWorksCard title="Step 2: Input your Assets"
                description="Input all the assets and liabilities you may have."
                image-url="/images/tools/goal_step2.webp" />
            <HowItWorksCard title="Step 3: Track and Analyze"
                description="Use our intuitive dashboard to track your goals and get insights."
                image-url="/images/tools/goal_step3.webp" />
        </div>

        <div class="flex justify-center mb-12">
            <ToolFoot title="Net Worth Calculating Made Simple"
                description="At Zurit, we believe in simplifying the process of calculating your net worth. Our tool is designed for easy navigation and user-friendly interfaces."
                buttonText="Go to Net Worth Calculator" buttonLink="#" />
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