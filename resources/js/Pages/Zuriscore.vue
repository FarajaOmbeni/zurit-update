<template>

    <Head title="Zuri Score" />
    <Navbar />
    <div class="mt-[7rem]">

        <ToolCardBig title="What is Zuri Score?"
            description="Zuri Score is a comprehensive financial health assessment tool that evaluates your overall financial well-being. It provides you with a personalized score and actionable insights to improve your financial standing."
            image-url="/images/tools/Questions.webp" />

        <WhyUseTool title="Why Use Zuri Score?"
            description="Our Zuri Score isn't just a number; it's your gateway to financial empowerment. It's designed to:"
            :bullet-points="['Assess your complete financial health profile',
                'Identify areas for financial improvement',
                'Track your financial progress over time',
                'Provide personalized recommendations for growth',
                'Compare your score with financial benchmarks']" image-url="/images/tools/networth1.webp" />

        <h2 class="text-4xl font-bold text-center mt-24 mb-6">How it Works</h2>
        <div class="flex flex-col lg:flex-row gap-6 justify-center items-center mb-16">
            <HowItWorksCard title="Step 1: Complete Assessment"
                description="Submit your Mpesa statement using our Prosperity Dashboard"
                image-url="/images/tools/goal_step1.webp" />
            <HowItWorksCard title="Step 2: Get Your Score"
                description="Receive your personalized report based on your financial statement."
                image-url="/images/tools/goal_step2.webp" />
            <HowItWorksCard title="Step 3: Improve & Track"
                description="Plan and restrategize from the report's insights."
                image-url="/images/tools/goal_step3.webp" />
        </div>

        <div class="flex justify-center mb-12">
            <ToolFoot title="Your Financial Health, Quantified"
                description="At Zurit, we believe in making financial health measurable and actionable. Discover where you stand and how to improve with our comprehensive scoring system."
                buttonText="Check your report" buttonLink="/user/zuriscore" />
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