<template>
    <Navbar />
    <div class="mt-36 mb-24">
        <div class="text-2xl md:ml-8 md:text-4xl lg:text-7xl md:text-left text-center font-bold lg:mb-6">Our
            <span class="text-yellow-600">Training Categories</span>
        </div>
        <div class="max-w-6xl mx-auto p-6">
            <!-- Navigation Tabs -->
            <div class="grid grid-cols-2 sm:flex gap-x-6 gap-y-2 mb-8 rounded-lg overflow-hidden">
                <TrainingTab v-for="tab in tabs" :key="tab.id" :label="tab.label" :isActive="activeTab === tab.id"
                    @click="activeTab = tab.id" />
            </div>


            <!-- Training Cards Flexbox -->
            <div class="flex flex-col items-center 
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

// Sample data structure with more details
const trainings = [
    {
        id: 1,
        type: 'individual',
        imageUrl: '/images/training/prosperity.jpg',
        title: 'PROSPERITY FUNDAMENTALS',
        description: 'This masterclass is a 5-week program tailored to individuals, covering the fundamentals of personal finance and investments.',
        details: [
            'Wealth building principles.',
            'Best practices for building wealth.',
            'How and where to invest',
            'How to systemize your investment processes.',
        ],
        attend: 'Individuals who are looking to build a solid financial foundation and grow their wealth.',
        price: '15,000',
        duration: '8 weeks'
    },
    {
        id: 2,
        type: 'quarterly',
        imageUrl: '/images/training/fundamentals.webp',
        title: 'Fundamentals of Investments',
        description: 'This is a 3-dayintense training for pension trustees focused on grounding them on the fundamentals of investments.',
        details: [
            'Wealth building principles.',
            'Best practices for building wealth.',
            'How and where to invest',
            'How to systemize your investment processes.',
        ],
        attend: 'Pension Trustees, Human Resources professionals, Sacco Board',
        duration: '6 weeks',
        price: '15,000'
    },
    {
        id: 3,
        type: 'quarterly',
        imageUrl: '/images/training/retirement.webp',
        title: 'Retirement Planning',
        description: 'Immerse yourself in this comprehensive training designed for pension trustees. The course provides a deep dive into retirement planning strategies, helping trustees understand the complexities of retirement income management and long-term investment planning.',
        details: [
            'Mental preparedness of life in retirement',
            'Portfolio Constructions',
            'Investment options and review of various asset class',
        ],
        attend: 'Staff of Corporate institutions, owner management',
        duration: '3-5 days',
        price: '79,000'
    },
    {
        id: 4,
        type: 'corporate',
        imageUrl: '/images/training/employee.webp',
        title: 'Employee Wellness',
        description: 'Dive into our Employee Wellness Program, a holistic initiative designed to promote and improve the health and well-being of your workforce. This program focuses on a variety of wellness aspects, including physical health, mental well-being, and work-life balance, aiming to create a healthier, happier, and more productive workplace.',
        details: [
            'Housing and Real Estate',
            'Debt management',
            'Investment options',
        ],
        attend: 'Corporate institutions looking to increasing employee engagement and productivity',
        duration: '1 day',
        price: '5,000 per person OR 45,000 (group of 10)'
    },
    {
        id: 5,
        type: 'corporate',
        imageUrl: '/images/training/retirement_pic.webp',
        title: 'Retirement Planning',
        description: 'Explore our Corporate Retirement Planning program, a comprehensive course designed to equip businesses with the knowledge and tools to establish and manage effective retirement plans. This program focuses on understanding the intricacies of retirement planning, including investment strategies, risk management, and regulatory compliance, with the goal of ensuring a secure and stable retirement for employees. ',
        details: [
            'Portfolio Constructions',
            'Goal Setting',
            'Investment options and review of various asset class',
        ],
        attend: 'Staff of Corporate institutions, owner management',
        duration: 'Tailor made per your needs',
    },
    {
        id: 6,
        type: 'wealthwave',
        imageUrl: '/images/training/wealthwave.webp',
        title: 'Wealth Wave Talks',
        description: 'Join our Wealth Wave Talks program to learn essential wealth creation and preservation strategies. This program offers insights into smooth wealth transfer to future generations and includes comprehensive training on wealth building and estate planning.',
        details: [
            'Getting it Right with your Investments',
            'Using Property to Build Wealth',
            'Pension Funds and Wealth Creation',
            'Capitals Markets Investing',
        ],
        attend: 'This program is ideal for individuals seeking to enhance their financial literacy, wealth creation and preservation strategies.',
        price: '1,500',
        more_info: 'Conducted Through Virtual sessions: Enter Contact details for more info'
    },
    {
        id: 7,
        type: 'wealthwave',
        imageUrl: '/images/training/asset_class.webp',
        title: 'Bi-Weekly Asset Classes',
        description: 'Our Asset Classes Training is a specialized program aimed at improving financial literacy and aiding in informed investment decisions. It`s suitable for both seasoned investors and beginners, providing the necessary knowledge to effectively navigate the complex world of asset classes.',
        details: [
            'Understanding the various asset classes',
            'Investing in Bonds & Treasury Bills',
            'Investing in Real Estate',
            'How to create a good portfolio',
        ],
        attend: 'This program is a golden opportunity for everyone! Whether you`re a seasoned investor, a beginner, or simply someone curious about the financial world',
        price: 'FREE',
        more_info: 'Conducted Through Virtual sessions: Enter Contact details for more info'
    },
    {
        id: 8,
        type: 'wealthwave',
        imageUrl: '/images/training/podcast.webp',
        title: 'Wealth Wave Podcast',
        description: 'Join our podcast to gain insights into the world of finance and investment. Whether you`re a seasoned investor or just starting your financial journey, our podcast offers valuable knowledge for everyone.',
        details: [
            'The Secrets of Successful Investing',
            'Financial Fitness for the Future',
            'Millennial Money Mastery',
            'Unlocking Your Wealth Potential',
            'The Path to Financial Independence',
        ],
        attend: 'Our podcast is tailored especially for the youth, providing accessible and engaging discussions on key financial topics to empower them with the knowledge they need to thrive in today`s economy.',
        more_info: 'Click on the button to visit our channel'
    },
    // Add more training items...
]

const tabs = [
    { id: 'individual', label: 'Individual' },
    { id: 'quarterly', label: 'Quarterly' },
    { id: 'corporate', label: 'Corporate' },
    { id: 'wealthwave', label: 'Wealth Wave' }
]

const activeTab = ref('individual')
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