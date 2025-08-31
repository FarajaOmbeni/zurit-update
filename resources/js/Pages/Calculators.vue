<template>

    <Head title="Financial Calculators" />
    <Navbar />
    <div class="mx-6 md:mx-12">
        <BackButton class="mt-6" />
    </div>
    <div class="mt-[7rem]">

        <ToolCardBig title="What are Financial Calculators?"
            description="Our Financial Calculators are powerful tools designed to help you make informed financial decisions. From loan calculations to investment projections, we provide comprehensive computational tools for all your financial planning needs."
            image-url="/images/tools/networth.webp" />

        <WhyUseTool title="Why Use Our Financial Calculators?"
            description="Our suite of financial calculators empowers you to make data-driven financial decisions. They're designed to:"
            :bullet-points="['Calculate loan payments and interest costs',
                'Project investment growth and returns',
                'Determine savings goals and timelines',
                'Estimate retirement planning needs',
                'Compare different financial scenarios']" image-url="/images/tools/invest.webp" />

        <h2 class="text-4xl font-bold text-center mt-24 mb-6">How it Works</h2>
        <div class="flex flex-col lg:flex-row gap-6 justify-center items-center mb-16">
            <HowItWorksCard title="Step 1: Choose Calculator"
                description="Select from our wide range of specialized financial calculators based on your needs."
                image-url="/images/tools/goal_step1.webp" />
            <HowItWorksCard title="Step 2: Enter Your Data"
                description="Input your financial information and parameters for accurate calculations."
                image-url="/images/tools/goal_step2.webp" />
            <HowItWorksCard title="Step 3: Get Results"
                description="Receive detailed calculations and insights to guide your financial decisions."
                image-url="/images/tools/goal_step3.webp" />
        </div>

        <div class="flex justify-center mb-12">
            <ToolFoot title="Calculate Your Financial Future"
                description="At Zurit, we provide you with the computational power to make smart financial decisions. Access professional-grade calculators designed for accuracy and ease of use."
                buttonText="Access Calculators" buttonLink="/user/calculators" />
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