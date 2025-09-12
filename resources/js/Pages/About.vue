<template>

    <Head title="About" />
    <Navbar />
    <div class="mx-6 md:mx-12">
        <BackButton class="mt-6" />
    </div>

    <div class="mx-6 md:mx-12 flex flex-col lg:flex-row gap-16">
        <!-- Left Section (Story & Team) -->
        <div class="lg:w-2/3 space-y-8 py-36">
            <div>
                <h3 class="font-bold text-3xl mb-4 text-center">Our Story</h3>
                <p class="text-gray-700 leading-relaxed text-2xl text-center mb-16">
                    At <b>Zurit Consulting</b>, we exist to make financial literacy practical and prosperity achievable.
                    From humble beginnings, we’ve grown into a trusted partner to <b>individuals, SACCOs, trustees,
                        MSMEs, and corporates</b>—
                    delivering training, tools, and advisory that turn good intentions into consistent money moves.
                    Rooted in our founder’s passion for inclusive wealth-building, we help clients set clear goals,
                    build simple systems,
                    and follow through with confidence.
                </p>
                <h4 class="font-semibold text-3xl mt-4 mb-6 text-center">What Sets Us Apart</h4>
                <ul class="list-disc pl-6 text-gray-700 text-2xl space-y-2">
                    <li><b>Seasoned experts:</b> real-world experience across personal finance, governance, and
                        investments.</li>
                    <li><b>Tailored strategies:</b> we start with your context—not a template.</li>
                    <li><b>Action you can keep:</b> clear plans, practical tools, and support that make progress stick.
                    </li>
                </ul>
                <p class="text-gray-700 leading-relaxed mt-2 text-2xl mb-16">
                    Work with a partner who meets you where you are—and moves you forward.
                </p>
            </div>

            <!-- Meet Our Team -->
            <div class="flex flex-col items-center">
                <h2 class="font-bold text-3xl mb-6">Meet Our Team</h2>
                <Profile name="Elizabeth Nkukuu" position="Founder & Team Lead" image="/images/team/liz.jpg"
                    linkedin_link="https://www.linkedin.com/in/elizabethnkukuu/" />
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-2">
                    <Profile name="Brian Salau" position="Manager" image="/images/team/brian.webp"
                        linkedin_link="https://www.linkedin.com/in/brian-salau-500255233/" />
                    <Profile name="Bryan Gitia" position="Investment Specialist" image="/images/team/gitia.webp"
                        linkedin_link="https://www.linkedin.com/in/bgitia/" />
                    <Profile name="Joel Mugonyi" position="Investment Analyst" image="/images/team/joel.png"
                        linkedin_link="https://www.linkedin.com/in/joel-mugonyi-79427323a/" />
                    <Profile name="Joseph Mukoko" position="Business Growth Coach" image="/images/team/joseph.jpg"
                        linkedin_link="https://www.linkedin.com/in/josephcmukoko/" />
                    <Profile name="Ombeni Faraja" position="Full Stack Developer" image="/images/team/faraja.webp"
                        linkedin_link="https://www.linkedin.com/in/ombenifaraja/" />
                    <Profile name="Noelle Jentrix" position="Client Relations" image="/images/team/noelle.webp"
                        linkedin_link="https://www.linkedin.com/in/noel-nabwire-097001265/?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app" />
                    <Profile name="Faith Mulinya" position="Client Relations" image="/images/team/faith.jpg"
                        linkedin_link="https://www.linkedin.com/in/faith-mulinya-a24580353/" />
                    <Profile name="Tugi Nyatuga" position="Digital Marketing Specialist" image="/images/team/tugi.jpg"
                        linkedin_link="https://www.linkedin.com/in/tugi-nyatuga-b981a4154/?originalSubdomain=ke" />
                </div>
            </div>

            <!-- Coaches -->
            <div class="flex flex-col items-center">
                <h2 class="font-bold text-3xl mb-6">Independent Financial Coaches</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-2">
                    <Profile name="Kyesubire Talitwala-Greigg" position="Coach" image="/images/team/kyesubire.jpg"
                        linkedin_link="https://www.linkedin.com/in/kyesubire/" />
                    <Profile name="Victor Muguna" position="Coach" image="/images/team/victor_muguna.jpg"
                        linkedin_link="https://www.linkedin.com/in/victor-muguna-7669409a/" />
                    <Profile name="Victor Namunyu" position="Coach" image="/images/team/victor_namunyu.jpg"
                        linkedin_link="https://www.linkedin.com/in/victor-namunyu-fmva%C2%AE-a818b2159/" />
                </div>
            </div>
        </div>

        <!-- Right Section (Who We Are & Tools) -->
        <div class="lg:w-1/3 space-y-10 p-5 xl:py-36 bg-yellow-100 mb-20">
            <div>
                <h2 class="text-2xl font-bold mb-2">Who We Are</h2>
                <p class="text-gray-700 leading-relaxed text-xl">
                    We’re a financial empowerment and wealth management partner. We translate complex finance into
                    everyday decisions—
                    from literacy to investment advisory—designing custom solutions that bridge the gap between income
                    and prosperity
                    so our clients can act with clarity.
                </p>
            </div>
            <div>
                <h2 class="text-2xl font-bold mb-2">Our Vision</h2>
                <p class="text-gray-700 leading-relaxed text-xl">To make financial prosperity the norm—not the
                    exception.</p>
            </div>
            <div>
                <h2 class="text-2xl font-bold mb-2">Our Mission</h2>
                <p class="text-gray-700 leading-relaxed text-xl">
                    To deliver simple, efficient, and impactful training and advisory that become the foundation for
                    thriving investments and confident, strategic decisions.
                </p>
            </div>
            <div>
                <h2 class="text-2xl font-bold mb-2">Our Values</h2>
                <ul class="list-disc pl-6 text-gray-700 text-xl space-y-1">
                    <li><b>Excellence</b> — We pursue the highest standards in everything we do.</li>
                    <li><b>Innovation</b> — We design solutions that are fresh, practical, and future‑focused.</li>
                    <li><b>Collaboration</b> — We believe real transformation happens in partnership.</li>
                    <li><b>Accountability</b> — We’re transparent, dependable, and results‑driven.</li>
                    <li><b>Impact</b> — We measure success by the positive change in people’s lives.</li>
                </ul>
            </div>

            <!-- Tools Section -->
            <div class="space-y-6">
                <h2 class="text-3xl font-bold text-center">Tools that Turn Plans into Progress</h2>
                <div class="flex justify-center">
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-1 gap-12 sm:gap-24 xl:gap-24 items-center justify-center">
                        <ToolCardSmall image="/images/tools/debt.webp" title="Debt Manager"
                            description="Track every facility, optimize repayments, and pay off faster—with clarity and control." />
                        <ToolCardSmall image="/images/tools/budget.webp" title="Budget Planner"
                            description="Build a living budget, stick to it, and fund what matters first." />
                        <ToolCardSmall image="/images/tools/investment.webp" title="Investment Planner"
                            description="Match goals to risk, allocate smartly, and invest with discipline." />
                        <ToolCardSmall image="/images/tools/networth.webp" title="Net Worth Calculator"
                            description="See the full picture—assets vs. liabilities—so growth is visible and intentional." />
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
import BackButton from '@/Components/Shared/BackButton.vue';
import { Head } from '@inertiajs/vue3';
</script>
