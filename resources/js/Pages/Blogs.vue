<template>

    <Head title="Blogs" />
    <Navbar />
    <div class="mt-32 mb-24  mx-4 ">
        <h1 class="text-3xl md:text-4xl lg:text-5xl xl:text-6xl md:text-start font-bold text-purple-500 mb-4 text-center">Our <span
                class="text-yellow-500">Blogs</span></h1>
        <div class="flex flex-col md:flex-row items-center md:items-start justify-center gap-12">
            <!-- Blog Cards Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 w-[70%] gap-8">
                <BlogCard image="/images/books/thrive.webp" title="Exciting Vue 3 Features"
                    content="Strategies for Financial Success
                        Building wealth in Kenya, like anywhere else, requires strategic planning, disciplined saving, and informed investing. Whether you aim to secure your financial future, prepare for retirement, or improve your quality of life, understanding how to grow your wealth in the Kenyan context is crucial.

                        1. Embrace the Power of Compound Interest

                        Compound interest is a vital tool for growing your wealth. In Kenya, taking advantage of compound interest can be done through various savings and investment vehicles, such as fixed deposit accounts, unit trusts, and savings cooperatives (SACCOs).

                        2. Leverage Chamas for Group Investments

                        Chamas, or informal savings groups, are a unique aspect of Kenyan financial culture. These groups pool their members' savings to invest in various ventures, from real estate to stock market investments. Joining a well-organized Chama can provide access to investment opportunities that might be challenging to pursue individually. Additionally, chamas often offer a platform for financial education and mutual support, helping members grow their wealth collectively.

                        3. Explore Agribusiness Opportunities

                        Agriculture is the backbone of Kenya's economy, and investing in agribusiness can be a profitable venture. Whether you're interested in crop farming, livestock, or value-added products like dairy or honey, agribusiness offers multiple avenues for wealth growth.

                        4. Practice Smart Saving and Spending

                        Wealth growth in Kenya also requires prudent financial management. Living within your means, avoiding unnecessary debt, and prioritizing savings are crucial steps. Automating your savings—such as setting up standing orders for your SACCO contributions or personal savings accounts—ensures that you consistently set aside money for the future. Additionally, consider using mobile banking and money management apps available in Kenya to track your expenses and manage your budget effectively.

                        Conclusion

                        Growing your wealth in Kenya involves a combination of strategic investing, disciplined saving, and informed decision-making. Remember, the journey to wealth is a marathon, not a sprint. With the right approach, patience, and a long-term perspective, you can achieve financial success and secure your future in Kenya.

                        At Zurit Consulting, we understand that the path to wealth can be complex and challenging. That's why we're here to guide you every step of the way. Join our Prosperity Fundamentals class starting on the 17th of this month, where we’ll equip you with the knowledge and tools you need to make informed financial decisions, maximize your investments, and build lasting wealth. Let Zurit Consulting help you turn your financial dreams into reality."
                    link="/blog/exciting-vue3-features" date="2024-08-19" />
                <BlogCard image="/images/books/thrive.webp" title="Vue 3 Performance Boosts"
                    content="Vue 3's optimized rendering engine brings faster updates..." link="/blog/vue3-performance"
                    date="2024-09-01" />
                <BlogCard image="/images/books/thrive.webp" title="Understanding Vue Reactivity"
                    content="A deep dive into Vue 3's reactivity system and how it works..." link="/blog/vue-reactivity"
                    date="2024-09-10" />
                <BlogCard image="/images/books/thrive.webp" title="State Management in Vue 3"
                    content="Exploring Pinia and Vuex for efficient state management..."
                    link="/blog/vue-state-management" date="2024-09-20" />
            </div>
            <!-- Search and Recent Posts Section -->
            <div class="">
                <div class="flex gap-4">
                    <input class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-purple-500" type="text"
                        placeholder="Search blogs..." />
                    <button
                        class="px-4 py-2 rounded-xl bg-gradient-to-r from-purple-700 to-yellow-500 text-white font-medium transition-all duration-300 ease-in-out hover:bg-gradient-to-r hover:from-yellow-500 hover:to-purple-700">
                        Search
                    </button>
                </div>
                <div class="">
                    <h1 class="text-xl font-bold text-gray-800">Recent Posts</h1>
                    <ul class="mt-2 space-y-2">
                        <li class="text-purple-700 hover:text-purple-900 transition-colors duration-300">
                            <a href="/blog/vue3-performance">🚀 Vue 3 Performance Boosts</a>
                        </li>
                        <li class="text-purple-700 hover:text-purple-900 transition-colors duration-300">
                            <a href="/blog/vue-reactivity">🔍 Understanding Vue Reactivity</a>
                        </li>
                        <li class="text-purple-700 hover:text-purple-900 transition-colors duration-300">
                            <a href="/blog/vue-state-management">🧩 State Management in Vue 3</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <Footer />
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import BlogCard from '@/Components/Shared/BlogCard.vue';
</script>
