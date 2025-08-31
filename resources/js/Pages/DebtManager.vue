<template>

    <Head title="Debt Manager" />
    <Navbar />
    <div class="mx-6 md:mx-12">
        <BackButton class="mt-6" />
    </div>
    <div class="mt-[7rem]">

        <ToolCardBig title="What is a Debt Manager?"
            description="A Debt Manager is a financial tool designed to assist in organizing and managing debts efficiently. It allows individuals and businesses to track and plan repayments, reducing the financial burden."
            image-url="/images/tools/debtmanager1.webp" />

        <WhyUseTool title="Why Use Our Debt Manager?"
            description="Our Debt Manager is a comprehensive financial solution that assists in managing and reducing debts. It's designed to:"
            :bullet-points="['Organize and track various debt accounts',
                'Provide a structured plan for debt repayment',
                'Offer insights into reducing debt efficiently',
                'Manage payment schedules and avoid late payments']" image-url="/images/tools/debtmanager2.webp" />

        <h2 class="text-4xl font-bold text-center mt-24 mb-6">How it Works</h2>
        <div class="flex flex-col lg:flex-row gap-6 justify-center items-center mb-16">
            <HowItWorksCard title="Step 1: Gather Information"
                description="Start by gathering details about your debts—amounts, interest rates, and due dates."
                image-url="/images/tools/debtstep1.webp" />
            <HowItWorksCard title="Step 2: Prioritize Debts"
                description="Evaluate debts and prioritize repayments—high-interest debts should take precedence."
                image-url="/images/tools/debtstep2.webp" />
            <HowItWorksCard title="Step 3: Create a Repayment Plan"
                description="Devise a structured repayment plan based on your financial situation and prioritization."
                image-url="/images/tools/debtstep3.webp" />
        </div>

        <div class="flex justify-center mb-12">
            <ToolFoot title="Debt Management Made Simple"
                description="At Zurit, we simplify debt management. Our tool provides: Clear overview of your debts, Customized repayment strategies, and Real-time tracking of debt reduction"
                buttonText="Go to Debt Manager" buttonLink="/user/debt" />
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