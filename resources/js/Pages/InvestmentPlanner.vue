<template>

    <Head title="Investment Planner" />
    <Navbar />
    <div class="mt-[7rem]">

        <ToolCardBig title="What is an Investment Planner?"
            description="An Investment Planner is a tool designed to help individuals and businesses make informed investment decisions by: Providing diversified investment options, Offering comprehensive market analysis, Assisting in long-term wealth creation"
            image-url="/images/tools/invest.webp" />

        <WhyUseTool title="Why Use Our Investment Planner?"
            description="Our Investment Planner isn't just a tool; it's a comprehensive solution designed to:"
            :bullet-points="['Offer diversified investment opportunities tailored to your needs',
                'Provide in-depth market analysis and trends',
                'Help in strategizing for long-term financial growth',
                'Help manage investments efficiently']" image-url="/images/tools/Questions.webp" />

        <h2 class="text-4xl font-bold text-center mt-24 mb-6">How it Works</h2>
        <div class="flex flex-col lg:flex-row gap-6 justify-center items-center mb-16">
            <HowItWorksCard title="Step 1: Set Your Investment Goals"
                description="Define your investment goals and risk tolerance to tailor your strategy."
                image-url="/images/tools/invest1.webp" />
            <HowItWorksCard title="Step 2: Diversify Your Portfolio"
                description="Explore various investment options to build a diversified portfolio."
                image-url="/images/tools/invest2.webp" />
            <HowItWorksCard title="Step 3: Monitor and Adjust"
                description="Regularly track and review your investments, adjusting for market changes."
                image-url="/images/tools/invest3.webp" />
        </div>

        <div class="flex justify-center mb-12">
            <ToolFoot title="Investment Planning Made Simple" description="At Zurit, we aim to simplify investment planning. Our tool provides:Personalized investment strategies,  Streamlined portfolio management, Clear insights into market trends, User-friendly interfaces for a seamless experience" buttonText="Go to Investment Planner" buttonLink="/user/invest" />
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