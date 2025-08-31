<template>

    <Head title="Training" />
    <Navbar />
    <div class="mx-6 md:mx-12">
        <BackButton class="mt-6" />
    </div>
    <div class="mt-36 mb-24">
        <div class="flex justify-center text-3xl md:text-4xl lg:text-5xl xl:text-6xl md:text-left font-bold lg:mb-6">Our
            Training Categories
        </div>
        <div class="max-w-6xl mx-auto p-6">
            <!-- Navigation Tabs -->
            <div class="grid grid-cols-2 sm:flex gap-x-6 gap-y-2 mb-8 rounded-lg overflow-hidden">
                <TrainingTab v-for="tab in tabs" :key="tab.id" :label="tab.label" :isActive="activeTab === tab.id"
                    @click="activeTab = tab.id" />
            </div>


            <!-- Training Cards Flexbox -->
            <div class="flex justify-center flex-col items-center 
            lg:flex-row gap-6 max-w-7xl">
                <TrainingCard v-for="training in filteredTrainings" :key="training.id" :imageUrl="training.imageUrl"
                    :title="training.title" :description="training.description" :price="training.price"
                    @learn-more="openModal(training)" class="w-full md:w-[48%] lg:w-[31%]" />
            </div>


            <!-- Modal -->
            <TrainingModal :is-open="isModalOpen" :training="selectedTraining" @close="closeModal"
                @enroll="openContactForm" />

            <!-- Contact Form Modal -->
            <EnrollModal :is-open="isContactFormOpen" @close="closeContactForm" @submit="handleContactSubmit" />
        </div>
    </div>
    <Footer />
</template>

<script setup>
import { ref, computed } from 'vue'
import TrainingTab from '@/Components/Shared/TrainingTab.vue'
import TrainingCard from '@/Components/Shared/TrainingCard.vue'
import TrainingModal from '@/Components/Shared/TrainingModal.vue'
import EnrollModal from '@/Components/Shared/EnrollModal.vue'
import Footer from '@/Components/Footer.vue'
import Navbar from '@/Components/Navbar.vue'
import BackButton from '@/Components/Shared/BackButton.vue'
import { Head } from '@inertiajs/vue3'

// Sample data structure with more details
const trainings = [
    {
        id: 1,
        type: 'corporate',
        imageUrl: '/images/training/prosperity.jpg',
        title: 'Prosperity Fundamentals',
        description: 'This masterclass is a 5-week program tailored to individuals, covering the fundamentals of personal finance and investments.',
        details: [
            'Wealth building principles.',
            'Best practices for building wealth.',
            'How and where to invest',
            'How to systemize your investment processes.',
        ],
        attends: ['Individuals who are looking to build a solid financial foundation and grow their wealth.'],
        price: '15,000 per person',
        duration: '5 weeks',
        link: 'https://dashboard.mailerlite.com/forms/1042116/152887660784912024/share'
    },
    {
        id: 2,
        type: 'corporate',
        imageUrl: '/images/training/wealthwave.webp',
        title: 'Prosperity Circles',
        description: 'Join our Prosperity Circles which runs every month to learn essential wealth creation and preservation strategies. This program offers insights into smooth wealth transfer to future generations and includes comprehensive training on wealth building and estate planning.',
        details: [
            'Getting it Right with your Investments',
            'Using Property to Build Wealth',
            'Pension Funds and Wealth Creation',
            'Capital Markets Investing',
        ],
        attends: ['This program is ideal for individuals seeking to enhance their financial literacy, wealth creation, and preservation strategies.'],
        duration: '3 hours',
        price: '3,000 per person',
        more_info: 'This training is done on a MONTHLY basis.',
        link: 'https://dashboard.mailerlite.com/forms/1042116/153540946091312633/share',
    },
    {
        id: 3,
        type: 'monthly',
        imageUrl: '/images/training/fundamentals.webp',
        title: 'Fundamentals of Investments',
        description: 'This is a 5-day intense training for pension trustees focused on grounding them on the fundamentals of investments.',
        details: [
            'Wealth building principles.',
            'Best practices for building wealth.',
            'How and where to invest',
            'How to systemize your investment processes.',
        ],
        attends: ['Pension Trustees, Human Resources professionals, Sacco Board'],
        duration: '5 days',
        price: '79,000 per person'
    },
    {
        id: 4,
        type: 'monthly',
        imageUrl: '/images/training/retirement.webp',
        title: 'Retirement Planning',
        description: 'Immerse yourself in this comprehensive training designed for pension trustees. The course provides a deep dive into retirement planning strategies, helping trustees understand the complexities of retirement income management and long-term investment planning.',
        details: [
            'Mental preparedness for life in retirement',
            'Portfolio Constructions',
            'Investment options and review of various asset classes',
        ],
        attends: ['Staff of Corporate institutions, owner management'],
        duration: '5 days',
        price: '79,000 per person',
        link: 'https://dashboard.mailerlite.com/forms/1042116/156257309697246706/share'
    },
    {
        id: 5,
        type: 'monthly',
        imageUrl: '/images/training/alternative.png',
        title: 'Alternative Investments',
        description: 'Explore the expanding universe of non-traditional assets and learn how to integrate them into institutional portfolios. This course demystifies alternative asset classes—private equity, hedge funds, real estate, infrastructure, commodities, and digital assets; equipping participants with the analytical tools needed to evaluate opportunities, manage risks, and meet long-term liability-matching objectives.',
        details: [
            'Overview of key alternative asset classes and their roles in a portfolio',
            'Risk-return characteristics and correlation benefits versus traditional equities and bonds',
            'Due-diligence frameworks, fee structures, and performance metrics',
            'Liquidity management, valuation challenges, and regulatory considerations',
            'Governance best practices for trustees: monitoring, reporting, and rebalancing'
        ],
        attends: ['Pension Trustees, Human Resources professionals, Sacco Board'],
        duration: '5 days',
        price: '79,000 per person',
        link: 'https://dashboard.mailerlite.com/forms/1042116/152526113162135515/share'
    },
    {
        id: 6,
        type: 'in-house',
        imageUrl: '/images/training/employee.webp',
        title: 'Employee Wellness',
        description: 'Dive into our Employee Wellness Program, a holistic initiative designed to promote and improve the health and well-being of your workforce. This program focuses on a variety of wellness aspects, including physical health, mental well-being, and work-life balance, aiming to create a healthier, happier, and more productive workplace.',
        details: [
            'Housing and Real Estate',
            'Debt management',
            'Investment options',
        ],
        attends: ['Corporate institutions looking to increase employee engagement and productivity'],
        price: '39,000 per 2 hours per group',
        price2: '74,100 per group',
        price3: '140,790 per group',
        link: 'https://dashboard.mailerlite.com/forms/1042116/156259861673280717/share',
    },
    {
        id: 7,
        type: 'in-house',
        imageUrl: '/images/training/retirement_pic.webp',
        title: 'Retirement Planning',
        description: 'Explore our Corporate Retirement Planning program, a comprehensive course designed to equip businesses with the knowledge and tools to establish and manage effective retirement plans. This program focuses on understanding the intricacies of retirement planning, including investment strategies, risk management, and regulatory compliance, with the goal of ensuring a secure and stable retirement for employees.',
        details: [
            'Portfolio Constructions',
            'Goal Setting',
            'Investment options and review of various asset classes',
        ],
        attends: ['Staff of Corporate institutions, owner management'],
        price: '39,000 per 2 hours per group',
        price2: '74,100 per group',
        price3: '140,790 per group',
        link: 'https://dashboard.mailerlite.com/forms/1042116/156257309697246706/share'
    },
    {
        id: 8,
        type: 'in-house',
        imageUrl: '/images/training/individual.avif',
        title: 'Individual Training',
        description: 'At Zurit Consulting, our one-on-one sessions are crafted to meet you exactly where you are—whether you\'re navigating a financial decision, growing a business, or seeking greater clarity in your personal wealth journey.',
        details: [
            'A customized financial review of your current position',
            'A personalized action plan with realistic, goal-driven steps',
            'Direct mentorship from our lead consultant',
            'Follow-up support to help you stay on track',
        ],
        attends: [
            'Individuals seeking expert financial guidance',
            'Entrepreneurs and professionals in transition',
            'Anyone looking to take control of their money and make strategic, confident decisions'
        ],
        price: '5,000 per session',
        link: 'https://dashboard.mailerlite.com/forms/1042116/156260898104673927/share'
    },
    {
        id: 9,
        type: 'free',
        imageUrl: '/images/training/asset_class.webp',
        title: 'Weekly Prosperity talks',
        description: 'Our Asset Classes Training is a specialized program aimed at improving financial literacy and aiding in informed investment decisions. It\'s suitable for both seasoned investors and beginners, providing the necessary knowledge to effectively navigate the complex world of asset classes.',
        details: [
            'Understanding the various asset classes',
            'Investing in Bonds & Treasury Bills',
            'Investing in Real Estate',
            'How to create a good portfolio',
        ],
        attends: ['Seasoned investors, beginners, or simply someone curious about the financial world'],
        price: 'FREE',
        more_info: 'Conducted through virtual sessions: Enter contact details for more info',
        link: 'https://dashboard.mailerlite.com/forms/1042116/156261774522320191/share'
    },
    // Add more training items...
]

const tabs = [
    { id: 'corporate', label: 'Corporate' },
    { id: 'monthly', label: 'Monthly' },
    { id: 'in-house', label: 'In-House Training' },
    { id: 'free', label: 'FREE Training' }
]

const activeTab = ref('corporate')
const isModalOpen = ref(false)
const selectedTraining = ref(null)
const isTrainingModalOpen = ref(false)
const isContactFormOpen = ref(false)

const filteredTrainings = computed(() => {
    return trainings.filter(training => training.type === activeTab.value)
})

const openModal = (training) => {
    selectedTraining.value = training
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
    selectedTraining.value = null
}

const handleEnroll = (trainingId) => {
    // Handle enrollment logic
    console.log('Enrolling in training:', trainingId)
    closeModal()
}

const openTrainingModal = (training) => {
    selectedTraining.value = training
    isTrainingModalOpen.value = true
}

const closeTrainingModal = () => {
    isTrainingModalOpen.value = false
    selectedTraining.value = null
}

const openContactForm = () => {
    isTrainingModalOpen.value = false
    isContactFormOpen.value = true
}

const closeContactForm = () => {
    isContactFormOpen.value = false
}

const handleContactSubmit = (formData) => {
    console.log('Form submitted:', formData)
    // Handle form submission (e.g., API call)
    closeContactForm()
    // Optionally show a success message
}
</script>