<template>

    <Head title="About" />
    <Navbar />

    <div class="mx-6 md:mx-12 flex flex-col lg:flex-row gap-16">
        <!-- Left Section (History & Team) -->
        <div class="lg:w-2/3 space-y-8 py-36">
            <div>
                <h3 class="font-bold text-3xl mb-4 text-center">Our History</h3>
                <p class="text-gray-700 leading-relaxed text-2xl text-center mb-16">
                    At Zurit Financial Consulting, we have a rich history of helping individuals and businesses achieve
                    their
                    financial goals. Our journey began with a vision to provide tailored financial solutions, and we
                    have stayed
                    true to that mission.
                </p>
                <h4 class="font-semibold text-3xl mt-4 mb-6">Why Choose Us:</h4>
                <ul class="list-disc pl-6 text-gray-700 text-2xl">
                    <li>Experienced Financial Experts</li>
                    <li>Customized Financial Strategies</li>
                    <li>Unwavering Commitment to Your Prosperity</li>
                </ul>
                <p class="text-gray-700 leading-relaxed mt-2 text-2xl mb-16">
                    We take pride in our experienced team of financial experts who provide personalized solutions to
                    meet your
                    unique financial challenges.
                </p>
            </div>

            <!-- Meet Our Team -->
            <div class="flex flex-col items-center">
                <h2 class="font-bold text-3xl mb-6">Meet Our Team</h2>
                <Profile name="Elizabeth Nkukuu" position="Founder - Team Leader" image="/images/team/liz.webp" />
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-32">
                    <Profile name="Brian Salau" position="Manager" image="/images/team/brian.webp" />
                    <Profile name="Ken Ndwigah" position="Head of Finance" image="/images/team/ken_img.webp" />
                    <Profile name="Noelle Jentrix" position="Clients Relations" image="/images/team/noelle.webp" />
                    <Profile name="Bryan Gitia" position="Investment Specialist" image="/images/team/gitia.webp" />
                    <Profile name="Bramwel" position="Developer" image="/images/team/bramwel.webp" />
                    <Profile name="Davis Otao" position="Full Stack Developer" image="/images/team/Davis.webp" />
                    <Profile name="Ombeni Faraja" position="Full Stack Developer" image="/images/team/faraja.webp" />

                    <Profile name="Churchil Wagwa" position="Multimedia Creative Arts"
                        image="/images/team/churchill.webp" />
                </div>
            </div>
        </div>

        <!-- Right Section (Who We Are & Tools) -->
        <div class="lg:w-1/3 space-y-10 p-5 xl:py-36 bg-yellow-100 mb-20">
            <div>
                <h2 class="text-2xl font-bold mb-2">Who We Are</h2>
                <p class="text-gray-700 leading-relaxed text-xl">
                    We are a bespoke Wealth Management Company focused on both individuals and corporates looking at
                    growing
                    wealth sustainably.
                </p>
            </div>
            <div>
                <h2 class="text-2xl font-bold mb-2">Our Vision</h2>
                <p class="text-gray-700 leading-relaxed text-xl">Making financial prosperity the norm.</p>
            </div>
            <div>
                <h2 class="text-2xl font-bold mb-2">Mission</h2>
                <p class="text-gray-700 leading-relaxed text-xl">
                    Deliver simple efficient Financial Trainings and Advisory Solutions which form the cornerstone of
                    thriving
                    investments.
                </p>
            </div>
            <div>
                <h2 class="text-2xl font-bold mb-2">Our Values</h2>
                <ul class="list-disc pl-6 text-gray-700 text-xl">
                    <li>Excellence</li>
                    <li>Innovation</li>
                    <li>Collaboration</li>
                    <li>Accountability</li>
                    <li>Impact</li>
                </ul>
            </div>

            <!-- Tools Section -->
            <div class="space-y-6">
                <h2 class="text-3xl font-bold text-center">Why Choose Our Tools</h2>
                <div class="flex justify-center">
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-1 gap-12 sm:gap-24 xl:gap-24 items-center justify-center">
                        <ToolCardSmall image="/images/tools/debt.webp" title="Debt Manager"
                            description="Our Debt Manager tool provides a comprehensive solution to track, organize, and optimize your debt repayment strategy." />
                        <ToolCardSmall image="/images/tools/budget.webp" title="Budget Planner"
                            description="Plan and manage your budget efficiently to achieve financial stability and growth." />
                        <ToolCardSmall image="/images/tools/investment.webp" title="Investment Planner"
                            description="Make informed investment decisions and build wealth sustainably." />
                        <ToolCardSmall image="/images/tools/networth.webp" title="Net Worth Calculator"
                            description="Track your assets and liabilities to measure financial progress." />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <Footer />
</template>

<script setup>
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import Profile from '@/Components/Shared/Profile.vue';
import ToolCardSmall from '@/Components/Shared/ToolCardSmall.vue';
import { Head } from '@inertiajs/vue3';
</script>
