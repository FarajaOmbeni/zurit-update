<template>

    <Head title="Financial Questionnaires" />
    <Navbar />
    <div class="mx-6 md:mx-12">
        <BackButton class="mt-6" />
    </div>
    <div class="mt-[7rem]">

        <ToolCardBig title="What are Financial Questionnaires?"
            description="Our Financial Questionnaires are comprehensive assessment tools designed to understand your financial profile, risk tolerance, and goals. They help create personalized financial strategies tailored to your unique situation."
            image-url="/images/tools/Questions.webp" />

        <WhyUseTool title="Why Take Our Financial Questionnaires?"
            description="Our questionnaires provide the foundation for personalized financial planning. They're designed to:"
            :bullet-points="['Assess your financial knowledge and experience',
                'Determine your risk tolerance and investment profile',
                'Identify your financial goals and priorities',
                'Understand your current financial situation',
                'Provide personalized recommendations and strategies']" image-url="/images/tools/budget.webp" />

        <h2 class="text-4xl font-bold text-center mt-24 mb-6">How it Works</h2>
        <div class="flex flex-col lg:flex-row gap-6 justify-center items-center mb-16">
            <HowItWorksCard title="Step 1: Select Questionnaire"
                description="Choose from our specialized questionnaires based on your financial planning needs."
                image-url="/images/tools/goal_step1.webp" />
            <HowItWorksCard title="Step 2: Complete Assessment"
                description="Answer detailed questions about your finances, goals, and preferences honestly."
                image-url="/images/tools/goal_step2.webp" />
            <HowItWorksCard title="Step 3: Receive Insights"
                description="Get personalized recommendations and strategies based on your responses."
                image-url="/images/tools/goal_step3.webp" />
        </div>

        <div class="flex justify-center mb-12">
            <ToolFoot title="Discover Your Financial Profile"
                description="At Zurit, we believe that understanding yourself is the first step to financial success. Our questionnaires provide the insights you need to make informed financial decisions."
                buttonText="Start Questionnaire" buttonLink="/user/questionnaires" />
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