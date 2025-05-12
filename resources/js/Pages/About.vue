<template>

    <Head title="About" />
    <Navbar />

    <div class="mx-6 md:mx-12 flex flex-col lg:flex-row gap-16">
        <!-- Left Section (History & Team) -->
        <div class="lg:w-2/3 space-y-8 py-36">
            <div>
                <h3 class="font-bold text-3xl mb-4 text-center">Our History</h3>
                <p class="text-gray-700 leading-relaxed text-2xl text-center mb-16">
                    At Zurit Consulting, our journey began with a bold vision: to make financial literacy and prosperity
                    accessible, practical, and sustainable for all. From humble beginnings, we've grown into a trusted
                    partner for <b>individuals, SACCOs, trustees, MSMEs, and corporates</b>—delivering tailored
                    financial training, tools, and advisory solutions that empower
                    people to make smarter money decisions. Rooted in our founder's passion for inclusive
                    wealth-building, Zurit has remained committed to helping clients not only set financial goals—but
                    achieve them with confidence.
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
                <Profile name="Elizabeth Nkukuu" position="Founder - Team Leader" image="/images/team/liz.webp"
                    linkedin_link="https://www.linkedin.com/in/elizabethnkukuu/" />
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-2">
                    <Profile name="Brian Salau" position="Manager" image="/images/team/brian.webp"
                        linkedin_link="https://www.linkedin.com/in/brian-salau-500255233/" />
                    <Profile name="Ombeni Faraja" position="Full Stack Developer" image="/images/team/faraja.webp"
                        linkedin_link="https://www.linkedin.com/in/ombenifaraja/" />
                    <Profile name="Bryan Gitia" position="Investment Specialist" image="/images/team/gitia.webp"
                        linkedin_link="https://www.linkedin.com/in/bgitia/" />
                    <Profile name="Noelle Jentrix" position="Clients Relations" image="/images/team/noelle.webp"
                        linkedin_link="https://www.linkedin.com/in/noel-nabwire-097001265/?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app" />
                    <Profile name="Tugi Nyatuga" position="Assistand Digital Marketer" image="/images/team/tugi.jpg"
                        linkedin_link="https://www.linkedin.com/in/tugi-nyatuga-b981a4154/?originalSubdomain=ke" />
                    <Profile name="Joel Mugonyi" position="Investment Analyst" image="/images/team/joel.jpg"
                        linkedin_link="https://www.linkedin.com/in/joel-mugonyi-79427323a/" />
                </div>
            </div>
        </div>

        <!-- Right Section (Who We Are & Tools) -->
        <div class="lg:w-1/3 space-y-10 p-5 xl:py-36 bg-yellow-100 mb-20">
            <div>
                <h2 class="text-2xl font-bold mb-2">Who We Are</h2>
                <p class="text-gray-700 leading-relaxed text-xl">
                    We are Zurit Consulting—a bespoke wealth management and financial empowerment firm committed to
                    helping individuals, corporates, and institutions grow and sustain wealth. From financial literacy
                    to investment advisory, we specialize in custom solutions that bridge the
                    gap between income and prosperity, empowering our clients to make confident, strategic decisions.
                </p>
            </div>
            <div>
                <h2 class="text-2xl font-bold mb-2">Our Vision</h2>
                <p class="text-gray-700 leading-relaxed text-xl">To make financial prosperity the norm, not the
                    exception.</p>
            </div>
            <div>
                <h2 class="text-2xl font-bold mb-2">Our Mission</h2>
                <p class="text-gray-700 leading-relaxed text-xl">
                    To deliver simple, efficient, and impactful financial training and advisory solutions that serve as
                    the foundation for thriving investments and empowered lives.
                </p>
            </div>
            <div>
                <h2 class="text-2xl font-bold mb-2">Our Values</h2>
                <ul class="list-disc pl-6 text-gray-700 text-xl">
                    <li>Excellence - We pursue the highest standards in everything we do.</li>
                    <li>Innovation - We design solutions that are fresh, practical, and future-focused.</li>
                    <li>Collaboration - We believe transformation happens through partnership.</li>
                    <li>Accountability - We are transparent, dependable, and committed to delivering results.</li>
                    <li>Impact - We measure success by the positive change we create in people's lives</li>
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
